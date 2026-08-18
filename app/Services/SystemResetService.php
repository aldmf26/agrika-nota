<?php

namespace App\Services;

use App\Models\SystemBackup;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class SystemResetService
{
    public function createBackup(User $user): SystemBackup
    {
        return Cache::lock('system-reset-operation', 120)->block(5, function () use ($user) {
            $data = $this->exportData();
            $fingerprint = $this->fingerprint($data);
            $createdAt = now();
            $token = Str::random(40);
            $path = 'backups/reset/reset-'.$createdAt->format('Ymd-His').'-'.$token.'.json';
            $document = [
                'metadata' => [
                    'format' => 'agrika-nota-reset-backup',
                    'version' => 1,
                    'created_at' => $createdAt->toIso8601String(),
                    'timezone' => config('app.timezone'),
                    'created_by' => ['id' => $user->id, 'name' => $user->name],
                    'data_fingerprint_sha256' => $fingerprint,
                    'photos_included' => false,
                    'counts' => [
                        'notas' => count($data['notas']),
                        'weekly_reviews' => count($data['weekly_reviews']),
                        'nota_archives' => count($data['nota_archives']),
                        'attachments_metadata' => collect($data['notas'])->sum(fn ($nota) => count($nota['attachments'])),
                    ],
                ],
                'data' => $data,
            ];
            $json = json_encode($document, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

            if (! Storage::disk('local')->put($path, $json)) {
                throw new RuntimeException('File backup tidak dapat disimpan.');
            }

            $stored = Storage::disk('local')->get($path);
            $decoded = json_decode($stored, true, 512, JSON_THROW_ON_ERROR);
            if (($decoded['metadata']['format'] ?? null) !== 'agrika-nota-reset-backup' || ! isset($decoded['data'])) {
                Storage::disk('local')->delete($path);
                throw new RuntimeException('Validasi file backup gagal.');
            }

            return SystemBackup::create([
                'token' => $token,
                'path' => $path,
                'checksum' => hash('sha256', $stored),
                'data_fingerprint' => $fingerprint,
                'file_size' => strlen($stored),
                'nota_count' => count($data['notas']),
                'created_by' => $user->id,
                'expires_at' => $createdAt->copy()->addDays(30),
            ]);
        });
    }

    public function assertDownloadable(SystemBackup $backup): void
    {
        if ($backup->expires_at->isPast() || ! Storage::disk('local')->exists($backup->path)) {
            throw new RuntimeException('Backup kedaluwarsa atau file tidak ditemukan.');
        }

        if (! hash_equals($backup->checksum, hash('sha256', Storage::disk('local')->get($backup->path)))) {
            throw new RuntimeException('Checksum backup tidak cocok.');
        }
    }

    public function reset(SystemBackup $backup): bool
    {
        return Cache::lock('system-reset-operation', 120)->block(5, function () use ($backup) {
            $backup->refresh();
            if ($backup->status !== 'ready' || $backup->used_at || $backup->expires_at->isPast()) {
                throw new RuntimeException('Backup tidak valid atau sudah digunakan.');
            }

            $this->assertDownloadable($backup);
            if (! hash_equals($backup->data_fingerprint, $this->fingerprint($this->exportData()))) {
                throw new RuntimeException('Data berubah setelah backup dibuat. Buat backup baru sebelum reset.');
            }

            DB::transaction(function () use ($backup) {
                foreach (['weekly_review_snapshots', 'weekly_reviews', 'nota_issues', 'deposit_logs', 'nota_items', 'nota_attachments', 'nota_archives', 'notas'] as $table) {
                    DB::table($table)->delete();
                }
                $backup->update(['status' => 'used', 'used_at' => now()]);
            });

            return ! Storage::disk('public')->exists('nota') || Storage::disk('public')->deleteDirectory('nota');
        });
    }

    public function pruneExpired(): int
    {
        $backups = SystemBackup::where('expires_at', '<=', now())->get();
        foreach ($backups as $backup) {
            Storage::disk('local')->delete($backup->path);
            $backup->delete();
        }
        return $backups->count();
    }

    private function fingerprint(array $data): string
    {
        return hash('sha256', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }

    private function exportData(): array
    {
        $users = DB::table('users')->pluck('name', 'id');
        $divisions = DB::table('divisis')->pluck('nama', 'id');
        $items = DB::table('nota_items')->orderBy('id')->get()->groupBy('nota_id');
        $attachments = DB::table('nota_attachments')->orderBy('id')->get()->groupBy('nota_id');
        $deposits = DB::table('deposit_logs')->orderBy('id')->get()->groupBy('nota_id');
        $issues = DB::table('nota_issues')->orderBy('id')->get()->groupBy('nota_id');

        $notas = DB::table('notas')->orderBy('id')->get()->map(function ($nota) use ($users, $divisions, $items, $attachments, $deposits, $issues) {
            $row = (array) $nota;
            $row['user_name'] = $users->get($nota->user_id);
            $row['approver_name'] = $users->get($nota->approver_id);
            $row['printed_by_name'] = $users->get($nota->printed_by);
            $row['divisi_name'] = $divisions->get($nota->divisi_id);
            $row['items'] = collect($items->get($nota->id, []))->map(function ($item) use ($divisions) {
                $item = (array) $item;
                $item['divisi_name'] = $divisions->get($item['divisi_id']);
                return $item;
            })->values()->all();
            $row['attachments'] = collect($attachments->get($nota->id, []))->map(function ($attachment) {
                $attachment = (array) $attachment;
                $attachment['file_included'] = false;
                return $attachment;
            })->values()->all();
            $row['deposit_logs'] = collect($deposits->get($nota->id, []))->map(function ($item) use ($divisions) {
                $item = (array) $item;
                $item['divisi_name'] = $divisions->get($item['divisi_id']);
                return $item;
            })->values()->all();
            $row['issues'] = collect($issues->get($nota->id, []))->map(function ($item) use ($users) {
                $item = (array) $item;
                $item['reported_by_name'] = $users->get($item['reported_by']);
                $item['resolved_by_name'] = $users->get($item['resolved_by']);
                return $item;
            })->values()->all();
            return $row;
        })->all();

        $snapshots = DB::table('weekly_review_snapshots')->orderBy('id')->get()->groupBy('weekly_review_id');
        $reviews = DB::table('weekly_reviews')->orderBy('id')->get()->map(function ($review) use ($users, $snapshots) {
            $row = (array) $review;
            $row['reviewed_by_name'] = $users->get($review->reviewed_by);
            $row['nota_ids'] = json_decode($row['nota_ids'] ?? '[]', true) ?? [];
            $row['snapshots'] = collect($snapshots->get($review->id, []))->map(function ($item) use ($users) {
                $item = (array) $item;
                $item['nota_ids'] = json_decode($item['nota_ids'] ?? '[]', true) ?? [];
                $item['reviewed_by_name'] = $users->get($item['reviewed_by']);
                return $item;
            })->values()->all();
            return $row;
        })->all();

        $archives = DB::table('nota_archives')->orderBy('id')->get()->map(function ($archive) {
            $row = (array) $archive;
            $row['full_data'] = json_decode($row['full_data'] ?? '{}', true) ?? $row['full_data'];
            return $row;
        })->all();

        return [
            'notas' => $notas,
            'weekly_reviews' => $reviews,
            'nota_archives' => $archives,
        ];
    }
}
