<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nota;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Tampilkan Rekap Laporan Bulanan per Divisi
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        
        // Ambil semua divisi aktif
        $divisis = Divisi::orderBy('nama')->get();
        
        // Ambil data summary (Approved only)
        // 1. Data dari nota biasa (non-split)
        $summaryBiasa = DB::table('notas')
            ->select('divisi_id', 'bulan', DB::raw('SUM(nominal) as total'))
            ->where('tahun', $tahun)
            ->where('status', 'approved')
            ->where('tipe', '!=', 'split')
            ->whereNotNull('divisi_id')
            ->groupBy('divisi_id', 'bulan')
            ->get();

        // 2. Data dari nota split (dari nota_items)
        $summarySplit = DB::table('nota_items')
            ->join('notas', 'nota_items.nota_id', '=', 'notas.id')
            ->select('nota_items.divisi_id', 'notas.bulan', DB::raw('SUM(nota_items.nominal) as total'))
            ->where('notas.tahun', $tahun)
            ->where('notas.status', 'approved')
            ->where('notas.tipe', 'split')
            ->groupBy('nota_items.divisi_id', 'notas.bulan')
            ->get();

        // Gabungkan data ke dalam array 2D matrix [divisi_id][bulan]
        $matrix = [];
        foreach ($divisis as $divisi) {
            $matrix[$divisi->id] = array_fill(1, 12, 0);
        }

        foreach ($summaryBiasa as $row) {
            if (isset($matrix[$row->divisi_id])) {
                $matrix[$row->divisi_id][$row->bulan] += $row->total;
            }
        }

        foreach ($summarySplit as $row) {
            if (isset($matrix[$row->divisi_id])) {
                $matrix[$row->divisi_id][$row->bulan] += $row->total;
            }
        }

        // Hitung total per bulan (footer)
        $monthlyTotals = array_fill(1, 12, 0);
        foreach ($matrix as $divRows) {
            foreach ($divRows as $bulan => $total) {
                $monthlyTotals[$bulan] += $total;
            }
        }

        // Hitung total per divisi (row end)
        $divisiTotals = [];
        foreach ($matrix as $divId => $divRows) {
            $divisiTotals[$divId] = array_sum($divRows);
        }

        $grandTotal = array_sum($divisiTotals);

        // Daftar tahun untuk filter (3 tahun terakhir + tahun depan jika ada data)
        $availableYears = Nota::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($availableYears)) $availableYears = [date('Y')];

        return view('admin.reports.index', compact(
            'tahun', 'divisis', 'matrix', 'monthlyTotals', 'divisiTotals', 'grandTotal', 'availableYears'
        ));
    }

    /**
     * Export data ke Excel (CSV)
     */
    public function export(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $type = $request->get('type', 'summary'); // summary atau detail

        if ($type === 'summary') {
            return $this->exportSummary($tahun);
        }

        return $this->exportDetail($tahun);
    }

    private function exportSummary($tahun)
    {
        $divisis = Divisi::orderBy('nama')->get();
        $summaryBiasa = DB::table('notas')
            ->select('divisi_id', 'bulan', DB::raw('SUM(nominal) as total'))
            ->where('tahun', $tahun)
            ->where('status', 'approved')
            ->where('tipe', '!=', 'split')
            ->whereNotNull('divisi_id')
            ->groupBy('divisi_id', 'bulan')
            ->get();

        $summarySplit = DB::table('nota_items')
            ->join('notas', 'nota_items.nota_id', '=', 'notas.id')
            ->select('nota_items.divisi_id', 'notas.bulan', DB::raw('SUM(nota_items.nominal) as total'))
            ->where('notas.tahun', $tahun)
            ->where('notas.status', 'approved')
            ->where('notas.tipe', 'split')
            ->groupBy('nota_items.divisi_id', 'notas.bulan')
            ->get();

        $matrix = [];
        foreach ($divisis as $divisi) {
            $matrix[$divisi->id] = ['nama' => $divisi->nama, 'data' => array_fill(1, 12, 0)];
        }

        foreach ($summaryBiasa as $row) {
            if (isset($matrix[$row->divisi_id])) {
                $matrix[$row->divisi_id]['data'][$row->bulan] += $row->total;
            }
        }
        foreach ($summarySplit as $row) {
            if (isset($matrix[$row->divisi_id])) {
                $matrix[$row->divisi_id]['data'][$row->bulan] += $row->total;
            }
        }

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"Rekap_Nota_{$tahun}.xlsx\"",
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function() use ($matrix, $tahun) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header
            $sheet->setCellValue('A1', "REKAP NOTA TAHUN {$tahun}");
            $sheet->mergeCells('A1:N1');
            $sheet->getStyle('A1')->getFont()->setBold(true);
            
            $headers = ["Divisi", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des", "Total"];
            $sheet->fromArray($headers, null, 'A2');
            $sheet->getStyle('A2:N2')->getFont()->setBold(true);

            $rowIdx = 3;
            foreach ($matrix as $row) {
                $rowData = [$row['nama']];
                $totalDivisi = 0;
                for ($i=1; $i<=12; $i++) {
                    $val = $row['data'][$i];
                    $rowData[] = $val;
                    $totalDivisi += $val;
                }
                $rowData[] = $totalDivisi;
                $sheet->fromArray($rowData, null, 'A' . $rowIdx);
                $rowIdx++;
            }

            // Set auto width
            foreach (range('A', 'N') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportDetail($tahun)
    {
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"Detail_Nota_Approved_{$tahun}.xlsx\"",
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function() use ($tahun) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', "DETAIL NOTA APPROVED TAHUN {$tahun}");
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true);

            $headers = ["Nomor Nota", "Tanggal", "Tipe", "Divisi", "Nominal", "Keterangan", "User", "Approved At"];
            $sheet->fromArray($headers, null, 'A2');
            $sheet->getStyle('A2:H2')->getFont()->setBold(true);

            $rowIdx = 3;
            $notas = Nota::with(['user', 'divisi', 'items.divisi'])
                ->where('tahun', $tahun)
                ->where('status', 'approved')
                ->orderBy('tanggal_nota')
                ->chunk(100, function($chunk) use ($sheet, &$rowIdx) {
                    foreach ($chunk as $nota) {
                        if ($nota->tipe === 'split') {
                            foreach ($nota->items as $item) {
                                $data = [
                                    $nota->nomor_nota,
                                    $nota->tanggal_nota->format('d/m/Y'),
                                    'Split',
                                    $item->divisi->nama ?? '-',
                                    $item->nominal,
                                    $nota->keterangan,
                                    $nota->user->name,
                                    $nota->approved_at ? $nota->approved_at->format('d/m/Y H:i') : '-'
                                ];
                                $sheet->fromArray($data, null, 'A' . $rowIdx);
                                $rowIdx++;
                            }
                        } else {
                            $data = [
                                $nota->nomor_nota,
                                $nota->tanggal_nota->format('d/m/Y'),
                                ucfirst($nota->tipe),
                                $nota->divisi->nama ?? '-',
                                $nota->nominal,
                                $nota->keterangan,
                                $nota->user->name,
                                $nota->approved_at ? $nota->approved_at->format('d/m/Y H:i') : '-'
                            ];
                            $sheet->fromArray($data, null, 'A' . $rowIdx);
                            $rowIdx++;
                        }
                    }
                });

            // Set auto width
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        };

        return response()->stream($callback, 200, $headers);
    }
}
