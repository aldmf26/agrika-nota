<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agrika Nota - Sistem Pencatatan Manual Nota</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: #fdfdfc;
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
            position: sticky;
            top: 0;
            z-index: 10;
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
            transition: color 0.2s;
        }

        nav a:hover {
            color: #5568d3;
        }

        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6rem 1.5rem;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1rem;
        }

        .btn-primary {
            background: white;
            color: #667eea;
        }

        .btn-primary:hover {
            background: #f3f4f6;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid white;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .section {
            padding: 5rem 1.5rem;
        }

        .section h2 {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 3rem;
            color: #1b1b18;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 3rem;
            height: 3rem;
            background: #667eea;
            color: white;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }

        .feature-card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            color: #4b5563;
            font-size: 0.95rem;
        }

        .steps {
            max-width: 600px;
            margin: 0 auto;
        }

        .step {
            margin-bottom: 2rem;
            display: flex;
            gap: 1.5rem;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            flex-shrink: 0;
        }

        .step h3 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .step p {
            color: #4b5563;
        }

        .cta {
            background: #f9fafb;
            padding: 5rem 1.5rem;
            text-align: center;
        }

        .cta h2 {
            margin-bottom: 2rem;
        }

        .cta p {
            margin-bottom: 2rem;
            color: #4b5563;
        }

        footer {
            background: #1b1b18;
            color: white;
            padding: 3rem 1.5rem;
            text-align: center;
            margin-top: 4rem;
        }

        footer p {
            margin: 0.5rem 0;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 1.8rem;
            }

            .features {
                grid-template-columns: 1fr;
            }

            nav .container {
                flex-direction: column;
                gap: 1rem;
            }

            nav a {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <nav>
        <div class="container">
            <div class="logo">🎯 Agrika Nota</div>
            <div>
                <a href="{{ url('/docs') }}">📖 Dokumentasi</a>
                @auth
                    <a href="{{ url('/nota') }}">📝 Input Nota</a>
                    <a href="{{ url('/dashboard') }}">📊 Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn"
                            style="background: none; color: #667eea; border: none; cursor: pointer; font-weight: 500;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="hero">
        <div class="container">
            <h1>Kelola Nota Manual dengan Mudah</h1>
            <p>Sistem pencatatan nota otomatis dengan workflow approver yang terstruktur</p>
            @auth
                <a href="{{ url('/nota/create') }}" class="btn btn-primary">Buat Nota Baru</a>
                <a href="{{ url('/nota') }}" class="btn btn-secondary" style="margin-left: 1rem;">Lihat Semua Nota</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Login untuk Memulai</a>
                <p class="mt-4 text-sm opacity-75">Hubungi Admin untuk pembuatan akun baru</p>
            @endauth
        </div>
    </div>

    <div class="section">
        <div class="container">
            <h2>Fitur Unggulan</h2>
            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3>5 Tipe Nota</h3>
                    <p>Split Tagihan, Revenue Sharing, Overpayment, dan tipe nota lainnya</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">✓</div>
                    <h3>Workflow Approver</h3>
                    <p>3 level persetujuan: Admin → Approver → Approved/Rejected</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Proses Cepat</h3>
                    <p>Input, submit, dan approval tanpa loading berlebihan</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Kalkulasi Otomatis</h3>
                    <p>Hitung split, revenue sharing, dan kelebihan bayar secara real-time</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📸</div>
                    <h3>Upload Lampiran</h3>
                    <p>Kelola foto bukti pembayaran hingga 5 file per nota</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📋</div>
                    <h3>Filter & Export</h3>
                    <p>Filter berdasarkan status, tipe, divisi, dan waktu pencatatan</p>
                </div>
            </div>
        </div>
    </div>

    <div class="cta">
        <div class="container">
            <h2>Cara Kerja</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div>
                        <h3>Input Nota</h3>
                        <p>Pilih tipe nota dan isi data sesuai kebutuhan. Unggah foto bukti pembayaran.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div>
                        <h3>Submit untuk Persetujuan</h3>
                        <p>Ubah status dari Draf menjadi Pending. Sistem akan memberitahu approver.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div>
                        <h3>Approval atau Penolakan</h3>
                        <p>Approver mengecek detail dan memilih approve atau reject dengan catatan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p><strong>&copy; 2026 Agrika Nota</strong> - Sistem Pencatatan Manual Nota Terpercaya</p>
        <p style="font-size: 0.9rem; color: #999;">Dibuat untuk memudahkan pencatatan dan workflow nota di perusahaan
            Anda.</p>
    </footer>
</body>

</html>
