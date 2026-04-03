<?php

namespace App\Services;

use App\Models\Nota;
use App\Models\NotaAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * Service untuk mengelola attachment (upload foto)
 * 
 * Prinsip: Tangani file operations, jangan di controller
 */
class AttachmentService
{
    /**
     * Upload lampiran foto untuk nota
     * 
     * Input: UploadedFile Array
     * Output: array of file paths
     */
    public function uploadAttachments(Nota $nota, array $uploadedFiles): array
    {
        $savedPaths = [];

        foreach ($uploadedFiles as $file) {
            $path = $this->storeFile($nota, $file);

            NotaAttachment::create([
                'nota_id' => $nota->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $savedPaths[] = $path;
        }

        return $savedPaths;
    }

    /**
     * Store single file ke storage
     * 
     * Path structure: nota/{notaId}/{timestamp}_{originalName}
     */
    private function storeFile(Nota $nota, UploadedFile $file): string
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $directory = "nota/{$nota->id}";

        return Storage::disk('public')->putFileAs($directory, $file, $fileName) ?? throw new \Exception('File upload failed');
    }

    /**
     * Delete attachment dari nota
     */
    public function deleteAttachment(NotaAttachment $attachment): void
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
    }

    /**
     * Get public URL untuk display di view
     */
    public function getAttachmentUrl(NotaAttachment $attachment): string
    {
        return Storage::disk('public')->url($attachment->file_path);
    }
}
