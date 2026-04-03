<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota {{ $nota->nomor_nota }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-item {
            font-size: 14px;
        }
        .info-item label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #f2f2f2;
            text-transform: uppercase;
        }
        .total-row {
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            text-align: center;
            gap: 20px;
        }
        .signature-box {
            height: 100px;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
            text-transform: uppercase;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="watermark">AGRIKA NOTA SYSTEM</div>

    <div class="container">
        <div class="header">
            <h1>AGRIKA NOTA OFFICIAL</h1>
            <p>Sistem Pencatatan Nota & Transaksi Internal</p>
        </div>

        <div class="info-grid">
            <div class="info-left">
                <div class="info-item"><label>No. Nota</label>: {{ $nota->nomor_nota ?? '-' }}</div>
                <div class="info-item"><label>Tanggal</label>: {{ $nota->tanggal_nota->format('d/m/Y') }}</div>
                <div class="info-item"><label>Divisi</label>: {{ $nota->divisi->nama ?? '-' }}</div>
            </div>
            <div class="info-right" style="text-align: right;">
                <div class="info-item"><label>Tipe Nota</label>: {{ strtoupper(str_replace('_', ' ', $nota->tipe)) }}</div>
                <div class="info-item"><label>Diajukan Oleh</label>: {{ $nota->user->name }}</div>
                <div class="info-item"><label>Dicetak Pada</label>: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div style="margin-bottom: 10px; font-weight: bold; text-transform: uppercase;">Detail Transaksi:</div>
        <table>
            <thead>
                <tr>
                    <th>Deskripsi / Keterangan</th>
                    <th style="text-align: right; width: 200px;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @if($nota->tipe === 'split')
                    @foreach($nota->items as $item)
                        <tr>
                            <td>Split Tagihan: {{ $item->divisi->nama }}</td>
                            <td style="text-align: right;">{{ number_format($item->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>{{ $nota->keterangan }}</td>
                        <td style="text-align: right;">{{ number_format($nota->nominal, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td style="text-align: right;">TOTAL AKHIR</td>
                    <td style="text-align: right;">Rp {{ number_format($nota->getNominalTotal(), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        @if($nota->tipe === 'revenue_sharing')
            <div style="font-size: 12px; margin-top: -20px; margin-bottom: 20px;">
                * Kalkulasi: Base {{ number_format($nota->base_amount, 0, ',', '.') }} x {{ $nota->persentase }}%
            </div>
        @endif

        @if($nota->tipe === 'kelebihan_bayar')
            <div style="font-size: 12px; margin-top: -20px; margin-bottom: 20px;">
                * Selisih Kelebihan: Rp {{ number_format($nota->selisih, 0, ',', '.') }}
            </div>
        @endif

        <div class="footer">
            <div>
                <p>Diajukan Oleh,</p>
                <div class="signature-box"></div>
                <p>({{ $nota->user->name }})</p>
            </div>
            <div>
                <p>Diketahui Oleh,</p>
                <div class="signature-box"></div>
                <p>(....................)</p>
            </div>
            <div>
                <p>Disetujui Oleh,</p>
                <div class="signature-box"></div>
                <p>({{ $nota->approver->name ?? '....................' }})</p>
            </div>
        </div>

        <div class="no-print" style="margin-top: 50px; text-align: center;">
            <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer;">Kembali</button>
            <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #333; color: #fff;">Cetak Lagi</button>
        </div>
    </div>
</body>
</html>
