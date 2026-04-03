<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Guide - Agrika Nota</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f9fafb;
            color: #1b1b18;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        nav {
            background: white;
            padding: 1.5rem 0;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: #667eea;
        }

        nav a {
            margin-left: 1.5rem;
            text-decoration: none;
            color: #667eea;
            font-weight: 500;
        }

        nav a:hover {
            color: #5568d3;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 1.5rem;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.95;
        }

        .section {
            padding: 3rem 1.5rem;
        }

        .section h2 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: #1b1b18;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }

        .guide-section {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .guide-section h3 {
            font-size: 1.3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .guide-section p {
            color: #4b5563;
            margin-bottom: 1rem;
            line-height: 1.8;
        }

        .table-wrapper {
            overflow-x: auto;
            margin: 1.5rem 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f3f4f6;
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            color: #1b1b18;
            border: 1px solid #e5e7eb;
        }

        td {
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            color: #4b5563;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .example-box {
            background: #eff6ff;
            border: 1px solid #93c5fd;
            border-left: 4px solid #3b82f6;
            padding: 1.5rem;
            border-radius: 0.375rem;
            margin: 1.5rem 0;
        }

        .example-box strong {
            color: #1e40af;
        }

        .example-box .label {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            margin-right: 0.5rem;
            font-weight: 600;
        }

        .formula {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 1rem;
            border-radius: 0.375rem;
            margin: 1rem 0;
            font-family: 'Courier New';
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .checklist {
            list-style: none;
            padding: 0;
        }

        .checklist li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }

        .checklist li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: 600;
        }

        footer {
            background: #1b1b18;
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
            margin-top: 4rem;
        }

        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <nav>
        <div class="container">
            <div class="logo">📚 Agrika Nota Docs</div>
            <div>
                <a href="{{ url('/docs') }}">← Kembali</a>
                <a href="{{ url('/') }}">Home</a>
            </div>
        </div>
    </nav>

    <div class="header">
        <div class="container">
            <h1>👨‍🎓 Panduan Lengkap 5 Tipe Nota</h1>
            <p>Pelajari setiap tipe nota dengan contoh, perhitungan, dan studi kasus praktis</p>
        </div>
    </div>

    <div class="section">
        <div class="container">

            <!-- TIPE 1: SPLIT TAGIHAN -->
            <div class="guide-section">
                <h3>1️⃣ Split Tagihan</h3>
                <p><strong>Fungsi:</strong> Membagi satu tagihan ke beberapa divisi berdasarkan jumlah rupiah yang
                    dibayarkan masing-masing divisi.</p>

                <div class="example-box">
                    <span class="label">CONTOH</span>
                    <p><strong>Skenario:</strong> Ada tagihan internet Rp 3.000.000 yang harus dibagi antara 3 divisi.
                    </p>
                    <ul style="margin-top: 0.5rem; margin-left: 1.5rem; color: #1e40af;">
                        <li>Divisi A membayar: Rp 1.000.000</li>
                        <li>Divisi B membayar: Rp 1.000.000</li>
                        <li>Divisi C membayar: Rp 1.000.000</li>
                    </ul>
                </div>

                <p><strong>Cara Input:</strong></p>
                <ul class="checklist" style="margin-bottom: 1rem;">
                    <li>Pilih tipe: "Split Tagihan"</li>
                    <li>Isi "Nominal Total": 3.000.000</li>
                    <li>Klik "Tambah Item" untuk setiap divisi</li>
                    <li>Isi jumlah per divisi (akan otomatis ter-total)</li>
                    <li>Unggah foto bukti pembayaran</li>
                    <li>Klik "Simpan" (status: Draft)</li>
                    <li>Klik "Submit" untuk approval</li>
                </ul>

                <p><strong>Field yang Diisi:</strong></p>
                <div class="table-wrapper">
                    <table>
                        <tr>
                            <th>Field</th>
                            <th>Contoh Input</th>
                            <th>Keterangan</th>
                        </tr>
                        <tr>
                            <td>Tanggal Nota</td>
                            <td>2026-04-02</td>
                            <td>Tanggal tagihan diterima</td>
                        </tr>
                        <tr>
                            <td>Nominal Total</td>
                            <td>3.000.000</td>
                            <td>Total tagihan yang dibayar</td>
                        </tr>
                        <tr>
                            <td>Divisi + Jumlah (Item)</td>
                            <td>A: 1.000.000 | B: 1.000.000 | C: 1.000.000</td>
                            <td>Breakdown per divisi</td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>Tagihan Internet Bulanan</td>
                            <td>Deskripsi singkat</td>
                        </tr>
                    </table>
                </div>

                <div class="formula">
                    Total Item = Jumlah Divisi A + Jumlah Divisi B + Jumlah Divisi C<br>
                    3.000.000 = 1.000.000 + 1.000.000 + 1.000.000 ✓
                </div>
            </div>

            <!-- TIPE 2: REVENUE SHARING -->
            <div class="guide-section">
                <h3>2️⃣ Revenue Sharing</h3>
                <p><strong>Fungsi:</strong> Membagi revenue/keuntungan berdasarkan persentase yang telah ditentukan.</p>

                <div class="example-box">
                    <span class="label">CONTOH</span>
                    <p><strong>Skenario:</strong> Revenue bulan April Rp 1.000.000.000 dibagi berdasarkan persentase
                        masing-masing divisi.</p>
                    <ul style="margin-top: 0.5rem; margin-left: 1.5rem; color: #1e40af;">
                        <li>Divisi Marketing: 40% = Rp 400.000.000</li>
                        <li>Divisi Sales: 35% = Rp 350.000.000</li>
                        <li>Divisi Operations: 25% = Rp 250.000.000</li>
                    </ul>
                </div>

                <p><strong>Cara Input:</strong></p>
                <ul class="checklist" style="margin-bottom: 1rem;">
                    <li>Pilih tipe: "Revenue Sharing"</li>
                    <li>Isi "Base Amount": 1.000.000.000 (total revenue)</li>
                    <li>Isi "Persentase Revenue" untuk masing-masing divisi</li>
                    <li>Sistem otomatis hitung: base_amount × persentase ÷ 100</li>
                    <li>Verifikasi hasil perhitungan</li>
                    <li>Submit untuk approval</li>
                </ul>

                <p><strong>Perhitungan Otomatis:</strong></p>
                <div class="formula">
                    Bagian Divisi = (Base Amount × Persentase) ÷ 100<br><br>
                    Contoh Marketing: (1.000.000.000 × 40) ÷ 100 = 400.000.000
                </div>

                <p><strong style="margin-top: 1.5rem;">Catatan Penting:</strong></p>
                <ul class="checklist">
                    <li>Total persentase harus = 100%</li>
                    <li>Desimal persentase didukung (contoh: 33.5%)</li>
                    <li>Sistem auto-calculate ketika input persentase</li>
                </ul>
            </div>

            <!-- TIPE 3: OVERPAYMENT -->
            <div class="guide-section">
                <h3>3️⃣ Overpayment (Kelebihan Bayar)</h3>
                <p><strong>Fungsi:</strong> Mencatat kelebihan pembayaran yang akan dikompensasi ke transaksi
                    berikutnya.</p>

                <div class="example-box">
                    <span class="label">CONTOH</span>
                    <p><strong>Skenario:</strong> Seharusnya bayar Rp 5.000.000 tapi pembayaran Rp 5.500.000. Kelebihan
                        Rp 500.000 ini dicatat.</p>
                </div>

                <p><strong>Cara Input:</strong></p>
                <ul class="checklist" style="margin-bottom: 1rem;">
                    <li>Pilih tipe: "Overpayment"</li>
                    <li>Isi "Nominal Seharusnya": 5.000.000</li>
                    <li>Isi "Nominal Dibayarkan": 5.500.000</li>
                    <li>Sistem otomatis hitung: 5.500.000 - 5.000.000 = 500.000</li>
                    <li>Kelebihan Rp 500.000 akan masuk deposit log</li>
                </ul>

                <div class="formula">
                    Kelebihan Bayar = Nominal Dibayarkan - Nominal Seharusnya<br>
                    500.000 = 5.500.000 - 5.000.000<br><br>
                    Kelebihan ini akan dikurangi otomatis dari tagihan berikutnya.
                </div>

                <p><strong style="margin-top: 1.5rem;">Tracking Kelebihan Bayar:</strong></p>
                <p>Setiap kelebihan bayar dicatat dalam "Deposit Log" dan dapat digunakan untuk:</p>
                <ul class="checklist">
                    <li>Mengurangi tagihan bulan berikutnya</li>
                    <li>Dijadikan referensi untuk verifikasi pembayaran</li>
                    <li>Ditampilkan dalam laporan detail nota</li>
                </ul>
            </div>

            <!-- TIPE 4 & 5: NOTE -->
            <div class="guide-section" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
                <h3 style="color: #b45309;">⚠️ Tipe Nota Tambahan</h3>
                <p>Sistem mendukung hingga 5 tipe nota. Tipe 4 dan 5 dapat dikustomisasi sesuai kebutuhan spesifik
                    perusahaan Anda. Hubungi administrator untuk setup tipe nota tambahan.</p>
            </div>

            <!-- QUICK REFERENCE -->
            <div class="guide-section">
                <h3>📋 Quick Reference - Perbandingan 3 Tipe Utama</h3>

                <div class="table-wrapper">
                    <table>
                        <tr>
                            <th>Aspek</th>
                            <th>Split Tagihan</th>
                            <th>Revenue Sharing</th>
                            <th>Overpayment</th>
                        </tr>
                        <tr>
                            <td><strong>Fungsi</strong></td>
                            <td>Bagi tagihan per jumlah rupiah</td>
                            <td>Bagi revenue per persentase</td>
                            <td>Catat kelebihan pembayaran</td>
                        </tr>
                        <tr>
                            <td><strong>Field Utama</strong></td>
                            <td>Total + item divisi</td>
                            <td>Base amount + persentase</td>
                            <td>Nominal seharusnya vs dibayarkan</td>
                        </tr>
                        <tr>
                            <td><strong>Kalkulasi</strong></td>
                            <td>Penjumlahan manual</td>
                            <td>Base × % ÷ 100 (auto)</td>
                            <td>Selisih (auto)</td>
                        </tr>
                        <tr>
                            <td><strong>Min Divisi</strong></td>
                            <td>2 divisi</td>
                            <td>1 divisi</td>
                            <td>1 divisi</td>
                        </tr>
                        <tr>
                            <td><strong>Lampiran</strong></td>
                            <td>Wajib</td>
                            <td>Opsional</td>
                            <td>Wajib</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- BEST PRACTICES -->
            <div class="guide-section">
                <h3>💡 Best Practices</h3>

                <div style="background: #f0fdf4; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                    <p style="color: #166534;"><strong>✓ Isi Keterangan Lengkap</strong> - Sertakan detail transaksi
                        agar approver mudah memahami.</p>
                </div>

                <div style="background: #f0fdf4; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                    <p style="color: #166534;"><strong>✓ Upload Foto Bukti</strong> - Lampir foto invoice/bukti
                        pembayaran untuk verifikasi.</p>
                </div>

                <div style="background: #f0fdf4; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                    <p style="color: #166534;"><strong>✓ Verifikasi Perhitungan</strong> - Cek ulang total sebelum
                        submit, terutama untuk revenue sharing.</p>
                </div>

                <div style="background: #f0fdf4; padding: 1rem; border-radius: 0.375rem;">
                    <p style="color: #166534;"><strong>✓ Submit Tepat Waktu</strong> - Jangan biarkan nota stuck di
                        status Draft. Submit segera setelah lengkap untuk proses approval.</p>
                </div>
            </div>

            <div
                style="text-align: center; margin-top: 3rem; padding: 2rem; background: #eff6ff; border-radius: 0.5rem;">
                <p style="color: #1e40af; margin-bottom: 1rem;">Ada pertanyaan lain? Lihat FAQ atau hubungi support.</p>
                <a href="{{ url('/docs') }}"
                    style="display: inline-block; background: #667eea; color: white; padding: 0.75rem 1.5rem; border-radius: 0.375rem; text-decoration: none; font-weight: 600;">←
                    Kembali ke Dokumentasi</a>
            </div>

        </div>
    </div>

    <footer>
        <p><strong>&copy; 2026 Agrika Nota</strong></p>
        <p style="font-size: 0.9rem; margin-top: 1rem; color: #999;">Sistem Pencatatan Manual Nota Terpercaya untuk
            Perusahaan Anda</p>
    </footer>
</body>

</html>
