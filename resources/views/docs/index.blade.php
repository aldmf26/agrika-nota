<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi - Agrika Nota</title>
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
            sticky: top;
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
            padding: 4rem 1.5rem;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        .section {
            padding: 4rem 1.5rem;
        }

        .section h2 {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: #1b1b18;
        }

        .intro-box {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            border-left: 4px solid #667eea;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .intro-box h3 {
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .intro-box p {
            color: #4b5563;
            line-height: 1.8;
        }

        .guide-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .guide-card {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
            cursor: pointer;
        }

        .guide-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .guide-card .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .guide-card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: #1b1b18;
        }

        .guide-card p {
            color: #4b5563;
            margin-bottom: 1rem;
        }

        .guide-card a {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 600;
        }

        .guide-card a:hover {
            background: #5568d3;
        }

        .step-box {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
        }

        .step-box strong {
            color: #667eea;
        }

        footer {
            background: #1b1b18;
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
            margin-top: 4rem;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8rem;
            }

            .guide-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <nav>
        <div class="container">
            <div class="logo">📚 Agrika Nota Docs</div>
            <div>
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ route('login') }}">Login</a>
            </div>
        </div>
    </nav>

    <div class="header">
        <div class="container">
            <h1>📖 Dokumentasi Agrika Nota</h1>
            <p>Panduan lengkap cara menggunakan sistem pencatatan nota manual</p>
        </div>
    </div>

    <div class="section">
        <div class="container">
            <h2>Apa itu Agrika Nota?</h2>

            <div class="intro-box">
                <h3>✨ Sistem Pencatatan Manual Nota Terpadu</h3>
                <p>Agrika Nota adalah aplikasi web untuk mencatat, mengelola, dan mengapprove nota manual dengan
                    workflow yang terstruktur. Sistem ini dirancang untuk mempermudah pencatatan transaksi dengan 5 tipe
                    nota berbeda, kalkulasi otomatis, dan proses approval multi-level.</p>
            </div>

            <h2 style="margin-top: 3rem;">🎯 Fitur Utama</h2>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-bottom: 2rem;">
                <div class="intro-box" style="border-left-color: #10b981;">
                    <h3 style="color: #10b981;">5 Tipe Nota</h3>
                    <p>Split Tagihan, Revenue Sharing, Overpayment, dan tipe lainnya dengan form dinamis</p>
                </div>
                <div class="intro-box" style="border-left-color: #f59e0b;">
                    <h3 style="color: #f59e0b;">Workflow Approver</h3>
                    <p>3 level approval: Admin input → Approver review → Final decision</p>
                </div>
                <div class="intro-box" style="border-left-color: #3b82f6;">
                    <h3 style="color: #3b82f6;">Kalkulasi Otomatis</h3>
                    <p>Real-time calculation untuk split, revenue sharing, kelebihan bayar</p>
                </div>
            </div>

            <h2 style="margin-top: 3rem; margin-bottom: 2rem;">📚 Panduan Pembelajaran</h2>

            <div class="guide-cards">
                <div class="guide-card">
                    <div class="icon">📖</div>
                    <h3>Panduan Lengkap 5 Tipe Nota</h3>
                    <p>Penjelasan detail setiap tipe nota dengan contoh praktis, perhitungan, dan studi kasus.</p>
                    <a href="{{ url('/docs/user-guide') }}">Pelajari →</a>
                </div>

                <div class="guide-card">
                    <div class="icon">✅</div>
                    <h3>Workflow Approval</h3>
                    <p>Cara kerja proses approval dan review nota. Panduan untuk admin dan approver.</p>
                    <a href="{{ url('/docs/workflow') }}">Baca →</a>
                </div>
            </div>

            <h2 style="margin-top: 4rem; margin-bottom: 2rem;">⚡ Proses Kerja Sistem</h2>

            <div class="step-box">
                <strong>Langkah 1: Login & Masuk ke Dashboard</strong>
                <p style="margin-top: 0.5rem; color: #4b5563;">Gunakan akun Anda untuk login ke aplikasi. Setelah
                    berhasil, Anda akan melihat dashboard dengan daftar nota.</p>
            </div>

            <div class="step-box">
                <strong>Langkah 2: Buat Nota Baru</strong>
                <p style="margin-top: 0.5rem; color: #4b5563;">Klik "Buat Nota Baru" dan pilih salah satu dari 5 tipe
                    nota. Isi form sesuai dengan jenis transaksi Anda.</p>
            </div>

            <div class="step-box">
                <strong>Langkah 3: Submit untuk Approval</strong>
                <p style="margin-top: 0.5rem; color: #4b5563;">Setelah form lengkap, ubah status dari "Draft" menjadi
                    "Pending" untuk dikirim ke approver.</p>
            </div>

            <div class="step-box">
                <strong>Langkah 4: Approval/Rejection</strong>
                <p style="margin-top: 0.5rem; color: #4b5563;">Approver akan mereview nota dan memilih untuk approve
                    atau reject dengan keterangan alasan.</p>
            </div>

            <div class="step-box">
                <strong>Langkah 5: Tracking & History</strong>
                <p style="margin-top: 0.5rem; color: #4b5563;">Pantau semua nota Anda dengan filter status, tipe,
                    divisi, dan periode waktu tertentu.</p>
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
