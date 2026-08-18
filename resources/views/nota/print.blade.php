<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota {{ $nota->nomor_nota }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0 !important;
        }

        html,
        body {
            width: 210mm;
            height: 297mm;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .print-sheet {
            box-sizing: border-box;
            width: 210mm;
            height: 297mm;
            margin: 0;
            display: grid;
            grid-template-rows: 1fr 1fr;
            overflow: hidden;
        }

        .container {
            box-sizing: border-box;
            width: 210mm;
            height: auto;
            min-height: 0;
            margin: 0;
            padding: 8mm 9mm 7mm;
            display: flex;
            flex-direction: column;
            position: relative;
            break-inside: avoid;
        }

        .container+.container {
            border-top: 1px dashed #555;
        }

        .container+.container::before {
            content: "✂  GUNTING";
            position: absolute;
            top: -7px;
            left: 8mm;
            padding: 0 4px;
            background: #fff;
            font-size: 8px;
            font-weight: bold;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 9px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 12px;
        }

        .info-item {
            font-size: 9px;
            display: grid;
            grid-template-columns: 68px 8px minmax(0, 1fr);
            align-items: start;
        }

        .info-item label {
            font-weight: bold;
        }

        .info-right .info-item {
            grid-template-columns: 82px 8px 120px;
            justify-content: end;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }

        th {
            background-color: #f2f2f2;
            text-transform: uppercase;
        }

        .total-row {
            font-weight: bold;
            font-size: 10px;
        }

        .footer {
            margin-top: auto;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            text-align: center;
            gap: 8px;
            font-size: 9px;
        }

        .footer p {
            margin: 4px 0;
        }

        .signature-box {
            height: 68px;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
        }

        .qr-box {
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .qr-box img {
            width: 62px;
            height: 62px;
            display: block;
        }

        .nota-description {
            white-space: pre-wrap;
        }

        .split-description {
            margin-bottom: 8px;
            padding: 6px;
            border: 1px solid #bbb;
            font-size: 9px;
        }

        .calculation-note {
            margin: 4px 0 8px;
            font-size: 8px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 56px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
            text-transform: uppercase;
        }

        .watermark-copy {
            transform: translate(-50%, -50%);
            color: rgba(220, 38, 38, 0.22);
            border: 3px solid rgba(220, 38, 38, 0.22);
            border-radius: 4px;
            padding: 4px 14px;
        }

        .watermark-copy-split {
            z-index: 1;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
                overflow: hidden;
            }

            .print-sheet {
                page-break-after: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="print-sheet">
        @foreach ([1, 2] as $copy)
            <div class="container">
                <div
                    class="watermark {{ $copy === 2 ? 'watermark-copy' : '' }} {{ $copy === 2 && $nota->tipe === 'split' ? 'watermark-copy-split' : '' }}">
                    {{ $copy === 1 ? 'AGRIKA NOTA SISTEM' : 'COPY' }}
                </div>
                <div class="header">
                    <h1>AGRIKA NOTA</h1>
                    <p>Sistem Pencatatan Nota & Transaksi Internal</p>
                </div>

                <div class="info-grid">
                    <div class="info-left">
                        <div class="info-item"><label>No.
                                Nota</label><span>:</span><span>{{ $nota->nomor_nota ?? '-' }}</span></div>
                        <div class="info-item">
                            <label>Tanggal</label><span>:</span><span>{{ $nota->tanggal_nota->format('d/m/Y') }}</span>
                        </div>
                        <div class="info-item">
                            <label>Divisi</label><span>:</span><span>{{ $nota->divisi->nama ?? '-' }}</span></div>
                    </div>
                    <div class="info-right">
                        <div class="info-item"><label>Tipe
                                Nota</label><span>:</span><span>{{ strtoupper(str_replace('_', ' ', $nota->tipe)) }}</span>
                        </div>
                        <div class="info-item"><label>Diajukan
                                Oleh</label><span>:</span><span>{{ $nota->user->name }}</span></div>
                        <div class="info-item"><label>Dicetak
                                Pada</label><span>:</span><span>{{ now()->format('d/m/Y H:i') }}</span></div>
                    </div>
                </div>

                <div style="margin-bottom: 10px; font-weight: bold; text-transform: uppercase;">Detail Transaksi:</div>
                @if ($nota->tipe === 'split')
                    <div class="split-description">
                        <strong>Keterangan:</strong> <span class="nota-description">{{ $nota->keterangan }}</span>
                    </div>
                @endif
                <table>
                    <thead>
                        <tr>
                            <th>Deskripsi / Keterangan</th>
                            <th style="text-align: right; width: 200px;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($nota->tipe === 'split')
                            @foreach ($nota->items as $item)
                                <tr>
                                    <td>Split Tagihan:
                                        {{ $item->divisi->nama }}{{ $item->persentase !== null ? ' (' . number_format($item->persentase, 2, ',', '.') . '%)' : '' }}
                                    </td>
                                    <td style="text-align: right;">{{ number_format($item->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="nota-description">{{ $nota->keterangan }}</td>
                                <td style="text-align: right;">{{ number_format($nota->nominal, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td style="text-align: right;">TOTAL AKHIR</td>
                            <td style="text-align: right;">Rp {{ number_format($nota->nominal, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                @if ($nota->tipe === 'revenue_sharing')
                    <div class="calculation-note">
                        * Kalkulasi: Base {{ number_format($nota->base_amount, 0, ',', '.') }} x
                        {{ $nota->persentase }}%
                    </div>
                @endif

                @if ($nota->tipe === 'kelebihan_bayar')
                    <div class="calculation-note">
                        * Selisih Kelebihan: Rp {{ number_format($nota->selisih, 0, ',', '.') }}
                    </div>
                @endif

                <div class="footer">
                    <div>
                        <p>Diajukan Oleh,</p>
                        <div class="qr-box">
                            <img src="{{ $creatorQrCode }}" alt="QR verifikasi pengaju">
                        </div>
                        <p>({{ $nota->user->name }})</p>
                    </div>
                    <div>
                        <p>Diketahui Oleh,</p>
                        <div class="signature-box"></div>
                        <p>(....................)</p>
                    </div>
                    <div>
                        <p>Disetujui Oleh,</p>
                        <div class="qr-box">
                            @if ($approvalQrCode)
                                <img src="{{ $approvalQrCode }}" alt="QR verifikasi persetujuan">
                            @endif
                        </div>
                        <p>({{ $nota->approver->name ?? '....................' }})</p>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    <div class="no-print" style="margin-top: 50px; text-align: center;">
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer;">Kembali</button>
        <button onclick="window.print()"
            style="padding: 10px 20px; cursor: pointer; background: #333; color: #fff;">Cetak Lagi</button>
    </div>
</body>

</html>
