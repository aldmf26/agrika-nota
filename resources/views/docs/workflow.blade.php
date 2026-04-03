<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workflow Approval - Agrika Nota</title>
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

        .workflow-diagram {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            padding: 2rem;
            border-radius: 0.5rem;
            margin: 2rem 0;
            text-align: center;
        }

        .workflow-step {
            display: inline-block;
            background: white;
            border: 2px solid #667eea;
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            margin: 0 0.5rem;
            font-weight: 600;
            color: #667eea;
            position: relative;
        }

        .workflow-step:not(:last-child)::after {
            content: "→";
            position: absolute;
            right: -1.5rem;
            font-size: 1.5rem;
            color: #667eea;
        }

        .workflow-diagram.mobile {
            padding: 1rem;
        }

        .workflow-step {
            display: block;
            margin: 1rem 0;
        }

        .workflow-step::after {
            display: none;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.5rem;
            color: white;
        }

        .status-draft {
            background: #99999999;
        }

        .status-pending {
            background: #f59e0b;
        }

        .status-approved {
            background: #10b981;
        }

        .status-rejected {
            background: #ef4444;
        }

        .role-box {
            background: white;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 0.375rem;
        }

        .role-box h4 {
            color: #667eea;
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
        }

        .role-box p {
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .role-box ul {
            margin-left: 1.5rem;
            color: #4b5563;
        }

        .role-box li {
            margin-bottom: 0.5rem;
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

        .info-box {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            border-left: 4px solid #3b82f6;
            padding: 1.5rem;
            border-radius: 0.375rem;
            margin: 1.5rem 0;
        }

        .info-box strong {
            color: #1e40af;
        }

        .warning-box {
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-left: 4px solid #f59e0b;
            padding: 1.5rem;
            border-radius: 0.375rem;
            margin: 1.5rem 0;
        }

        .warning-box strong {
            color: #92400e;
        }

        .success-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-left: 4px solid #10b981;
            padding: 1.5rem;
            border-radius: 0.375rem;
            margin: 1.5rem 0;
        }

        .success-box strong {
            color: #15803d;
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

        .note-box {
            background: #f3f4f6;
            border-left: 4px solid #667eea;
            padding: 1rem;
            border-radius: 0.375rem;
            margin: 1rem 0;
            font-size: 0.95rem;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
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

            .workflow-diagram {
                display: none;
            }

            .workflow-diagram.mobile {
                display: block;
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
            <h1>✅ Workflow Approval Nota</h1>
            <p>Panduan lengkap proses approval dan review nota dengan 3 level workflow</p>
        </div>
    </div>

    <div class="section">
        <div class="container">

            <!-- OVERVIEW -->
            <div class="guide-section">
                <h3>📊 Gambaran Umum Workflow</h3>
                <p>Setiap nota yang dibuat harus melalui proses approval multi-level untuk memastikan validitas dan
                    keakuratan data. Sistem ini dirancang untuk memberikan kontrol dan transparansi dalam setiap tahap
                    proses.</p>

                <div class="workflow-diagram" style="display: none;">
                    <div class="workflow-step">📝 Draft</div>
                    <div class="workflow-step">🔄 Pending</div>
                    <div class="workflow-step">✅ Approved</div>
                </div>

                <div class="workflow-diagram mobile">
                    <div class="workflow-step">📝 Draft</div>
                    <div style="color: #667eea; margin: 0.5rem 0;">↓</div>
                    <div class="workflow-step">🔄 Pending</div>
                    <div style="color: #667eea; margin: 0.5rem 0;">↓</div>
                    <div class="workflow-step">✅ Approved / ❌ Rejected</div>
                </div>

                <div class="info-box">
                    <strong>💡 Status Nota:</strong> Setiap nota memiliki satu dari 4 status: Draft, Pending, Approved,
                    atau Rejected.
                </div>
            </div>

            <!-- STATUS EXPLANATION -->
            <div class="guide-section">
                <h3>📋 Penjelasan Status Nota</h3>
                <div class="table-wrapper">
                    <table>
                        <tr>
                            <th>Status</th>
                            <th>Deskripsi</th>
                            <th>Siapa yang Bisa Akses</th>
                            <th>Tindakan</th>
                        </tr>
                        <tr>
                            <td><span class="status-badge status-draft">DRAFT</span></td>
                            <td>Nota baru yang belum disubmit untuk approval</td>
                            <td>Pembuat nota saja</td>
                            <td>Edit, Hapus, Submit</td>
                        </tr>
                        <tr>
                            <td><span class="status-badge status-pending">PENDING</span></td>
                            <td>Nota sudah disubmit menunggu approval dari approver</td>
                            <td>Pembuat & Approver</td>
                            <td>View, Download PDF</td>
                        </tr>
                        <tr>
                            <td><span class="status-badge status-approved">APPROVED</span></td>
                            <td>Nota sudah disetujui oleh approver</td>
                            <td>Semua user</td>
                            <td>View, Download PDF</td>
                        </tr>
                        <tr>
                            <td><span class="status-badge status-rejected">REJECTED</span></td>
                            <td>Nota ditolak oleh approver dengan alasan tertentu</td>
                            <td>Pembuat & Approver</td>
                            <td>View, Edit, Resubmit</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- WORKFLOW STAGES -->
            <div class="guide-section">
                <h3>🔄 3 Level Workflow Approval</h3>

                <h4 style="color: #1b1b18; font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 1rem;">Level 1:
                    Pembuatan & Submit Otomatis</h4>
                <div class="role-box">
                    <h4>👤 Role: Admin / Input Staff</h4>
                    <p><strong>Tanggung Jawab:</strong></p>
                    <ul>
                        <li>Membuat nota baru dengan type yang sesuai</li>
                        <li>Mengisi semua field yang diperlukan dengan benar</li>
                        <li>Memastikan perhitungan sudah sesuai (sistem sudah otomatis)</li>
                        <li>Upload bukti pendukung (foto, dokumen) - <strong>WAJIB</strong></li>
                        <li>Klik "Simpan" → Nota LANGSUNG ke status Pending</li>
                        <li>Nota otomatis dikirim ke approver untuk review</li>
                        <li>Anda masih bisa EDIT selama approver belum approve/reject</li>
                    </ul>

                    <div class="note-box">
                        <strong>💡 Tips:</strong> Tidak perlu tombol Submit lagi! Setelah klik Simpan, nota langsung
                        masuk ke approver. Anda bisa edit kapan saja sebelum approver memberikan keputusan.
                    </div>
                </div>

                <h4 style="color: #1b1b18; font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 1rem;">Level 2: Review
                    Awal (First Approver)</h4>
                <div class="role-box">
                    <h4>👤 Role: Approver 1 / Supervisor</h4>
                    <p><strong>Tanggung Jawab:</strong></p>
                    <ul>
                        <li>Menerima notifikasi ketika ada nota yang pending</li>
                        <li>Melakukan review detail terhadap nota dan itemnya</li>
                        <li>Memverifikasi kelengkapan dokumen pendukung</li>
                        <li>Mengecek perhitungan dan breakdown per divisi</li>
                        <li>Memberikan feedback atau catatan jika ada masalah</li>
                        <li>Approve jika sudah sesuai atau Reject dengan alasan</li>
                    </ul>

                    <div class="warning-box">
                        <strong>⚠️ Penting:</strong> Jika ada ketidaksesuaian, jangan langsung approve. Berikan feedback
                        yang jelas agar pembuat nota bisa memperbaiki.
                    </div>
                </div>

                <h4 style="color: #1b1b18; font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 1rem;">Level 3:
                    Approval Final (Final Decision)</h4>
                <div class="role-box">
                    <h4>👤 Role: Finance Head / Final Approver</h4>
                    <p><strong>Tanggung Jawab:</strong></p>
                    <ul>
                        <li>Menerima nota yang sudah di-approve level 1</li>
                        <li>Melakukan review komprehensif untuk final decision</li>
                        <li>Memastikan kesesuaian dengan kebijakan perusahaan</li>
                        <li>Memberikan approval final atau rejection</li>
                        <li>Dokumentasi keputusan untuk audit trail</li>
                    </ul>

                    <div class="success-box">
                        <strong>✔️ Note:</strong> Approval final ini menentukan status nota apakah "Approved" atau
                        "Rejected". Keputusan ini bersifat final.
                    </div>
                </div>
            </div>

            <!-- PROCESS FLOW FOR USERS -->
            <div class="guide-section">
                <h3>👨‍💼 Panduan Proses untuk Input Admin</h3>

                <p><strong>Langkah-langkah membuat nota (auto-submit ke approver):</strong></p>

                <div class="table-wrapper">
                    <table>
                        <tr>
                            <th>Langkah</th>
                            <th>Tindakan</th>
                            <th>Status Nota</th>
                            <th>Keterangan</th>
                        </tr>
                        <tr>
                            <td><strong>1</strong></td>
                            <td>Buka dashboard → Klik "Buat Nota Baru"</td>
                            <td>-</td>
                            <td>Pilih tipe nota yang sesuai</td>
                        </tr>
                        <tr>
                            <td><strong>2</strong></td>
                            <td>Isi form dengan benar dan lengkap</td>
                            <td>-</td>
                            <td>Semua field wajib diisi, lampiran WAJIB</td>
                        </tr>
                        <tr>
                            <td><strong>3</strong></td>
                            <td>Upload bukti pendukung</td>
                            <td>-</td>
                            <td>Minimal 1 file (foto, PDF, dokumen) - <strong>WAJIB</strong></td>
                        </tr>
                        <tr>
                            <td><strong>4</strong></td>
                            <td>Klik "Simpan" (auto submit ke approver)</td>
                            <td>🔄 Pending</td>
                            <td>Nota LANGSUNG dikirim ke approver, TIDAK ADA step submit tambahan</td>
                        </tr>
                        <tr>
                            <td><strong>5</strong></td>
                            <td>Menunggu keputusan approver</td>
                            <td>🔄 Pending</td>
                            <td>Anda masih bisa EDIT selama dalam review. Cek notifikasi status.</td>
                        </tr>
                        <tr>
                            <td><strong>6</strong></td>
                            <td>Jika Approved → Done. Jika Reject → Edit & resubmit</td>
                            <td>✅ Approved / ❌ Rejected</td>
                            <td>Approved = locked. Reject = bisa edit & resubmit</td>
                        </tr>
                    </table>
                </div>

                <div class="info-box">
                    <strong>🎯 Alur Singkat:</strong><br>
                    Input Nota → Simpan (auto Pending) → Approver Review → Approve/Reject → Done/Edit & Resubmit
                </div>

                <div class="info-box">
                    <strong>🔔 Notifikasi:</strong> Anda akan menerima email dan notifikasi in-app ketika ada perubahan
                    status nota (Approved atau Rejected).
                </div>
            </div>

            <!-- PROCESS FLOW FOR APPROVERS -->
            <div class="guide-section">
                <h3>✅ Panduan Proses untuk Approver</h3>

                <p><strong>Langkah-langkah melakukan approval nota:</strong></p>

                <div class="table-wrapper">
                    <table>
                        <tr>
                            <th>Langkah</th>
                            <th>Tindakan</th>
                            <th>Kondisi</th>
                            <th>Hasil</th>
                        </tr>
                        <tr>
                            <td><strong>1</strong></td>
                            <td>Login ke dashboard approver</td>
                            <td>-</td>
                            <td>Lihat section "Nota Menunggu Review"</td>
                        </tr>
                        <tr>
                            <td><strong>2</strong></td>
                            <td>Klik nota untuk view detail</td>
                            <td>Status Pending</td>
                            <td>Buka detail page dengan semua informasi</td>
                        </tr>
                        <tr>
                            <td><strong>3</strong></td>
                            <td>Review: cek tipe, nominal, items, dokumen</td>
                            <td>-</td>
                            <td>Pastikan semuanya sesuai & valid</td>
                        </tr>
                        <tr>
                            <td><strong>4a</strong></td>
                            <td>Jika OK → Klik "Approve"</td>
                            <td>Sudah diperiksa & sesuai</td>
                            <td>Status berubah Approved, pembuat nota notified</td>
                        </tr>
                        <tr>
                            <td><strong>4b</strong></td>
                            <td>Jika ada masalah → Klik "Reject"</td>
                            <td>Ada error atau data tidak sesuai</td>
                            <td>Beri alasan clear, pembuat bisa edit & resubmit</td>
                        </tr>
                    </table>
                </div>

                <div class="warning-box">
                    <strong>⚠️ Penting untuk Approver:</strong>
                    <ul style="margin-top: 0.75rem; margin-left: 1.5rem;">
                        <li>Jangan approve nota yang belum lengkap atau data tidak jelas</li>
                        <li>Selalu berikan alasan jika reject untuk feedback pembuat</li>
                        <li>Cek attachment dan dokumen pendukung dengan teliti</li>
                        <li>Verifikasi perhitungan (sistem sudah otomatis tapi tetap cek)</li>
                    </ul>
                </div>
            </div>

            <!-- REJECTION & REVISION -->
            <div class="guide-section">
                <h3>🔄 Proses Revision Jika Reject</h3>

                <p>Ketika nota di-reject, pembuat nota bisa melakukan revisi dan resubmit:</p>

                <ul class="checklist" style="margin: 1rem 0;">
                    <li>Nota Rejected muncul di dashboard dengan status "Rejected"</li>
                    <li>Lihat alasan rejection dari approver di dalam nota</li>
                    <li>Klik "Edit" untuk membuka form edit</li>
                    <li>Lakukan perbaikan sesuai feedback yang diberikan</li>
                    <li>Klik "Simpan" untuk update (auto-resubmit ke approver)</li>
                    <li>Tidak perlu tombol Submit tambahan - langsung go to approver</li>
                    <li>Approver akan mereview revisi Anda</li>
                </ul>

                <div class="note-box">
                    <strong>💡 Tips Revisi:</strong> Baca dengan teliti alasan rejection, kemudian perbaiki spesifik
                    bagian yang bermasalah. Tambahkan catatan di field keterangan menjelaskan perbaikan yang dilakukan
                    untuk memudahkan approver.
                </div>
            </div>

            <!-- BEST PRACTICES -->
            <div class="guide-section">
                <h3>🎯 Best Practices untuk Efisiensi</h3>

                <div class="grid-2">
                    <div>
                        <h4 style="color: #667eea; margin-bottom: 1rem;">Untuk Input Admin:</h4>
                        <ul class="checklist">
                            <li>Input data dengan teliti & lengkap</li>
                            <li>Gunakan deskripsi yang jelas & singkat</li>
                            <li>Upload minimal 1 dokumen berkaitan</li>
                            <li>Review sebelum submit (gunakan preview)</li>
                            <li>Jika reject, perhatikan feedback dengan baik</li>
                            <li>Jangan duplicate nota, pakai edit jika perlu</li>
                        </ul>
                    </div>
                    <div>
                        <h4 style="color: #667eea; margin-bottom: 1rem;">Untuk Approver:</h4>
                        <ul class="checklist">
                            <li>Review dengan konsisten & detail</li>
                            <li>Berikan feedback yang konstruktif</li>
                            <li>Jelaskan alasan approval & rejection</li>
                            <li>Cek dokumen attachment dengan teliti</li>
                            <li>Verifikasi breakdown & perhitungan</li>
                            <li>Respond secepat mungkin (SLA 24 jam)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TIPS & TROUBLESHOOTING -->
            <div class="guide-section">
                <h3>❓ Tips & Troubleshooting</h3>

                <div class="guide-section" style="background: #f9fafb; border-left: 4px solid #3b82f6;">
                    <h4 style="color: #1e40af;">Q: Bagaimana jika nota sudah disubmit tapi ada kesalahan?</h4>
                    <p style="margin-top: 0.75rem;">A: Hubungi approver untuk reject nota, atau tunggu sampai di-reject
                        oleh approver. Setelah di-reject, Anda bisa edit dan resubmit. Nota yang pending tidak bisa
                        diedit.</p>
                </div>

                <div class="guide-section" style="background: #f9fafb; border-left: 4px solid #3b82f6;">
                    <h4 style="color: #1e40af;">Q: Berapa lama proses approval?</h4>
                    <p style="margin-top: 0.75rem;">A: Target SLA adalah 24 jam kerja. Tergantung tingkat kesibukan
                        approver. Jika urgent, bisa koordinasi langsung dengan approver.</p>
                </div>

                <div class="guide-section" style="background: #f9fafb; border-left: 4px solid #3b82f6;">
                    <h4 style="color: #1e40af;">Q: Bagaimana jika nota diterima approver tapi belum ada notifikasi?</h4>
                    <p style="margin-top: 0.75rem;">A: Cek di dashboard bagian "Riwayat Nota" atau pilter status.
                        Notifikasi email mungkin masuk ke spam, cek folder spam Anda.</p>
                </div>

                <div class="guide-section" style="background: #f9fafb; border-left: 4px solid #3b82f6;">
                    <h4 style="color: #1e40af;">Q: Dimana saya lihat nota yang sudah diapprove?</h4>
                    <p style="margin-top: 0.75rem;">A: Di dashboard, gunakan filter "Status = Approved" untuk melihat
                        semua nota yang sudah diapprove. Anda bisa download PDF untuk arsip.</p>
                </div>

                <div class="guide-section" style="background: #f9fafb; border-left: 4px solid #3b82f6;">
                    <h4 style="color: #1e40af;">Q: Bisa tidak cancel nota yang sudah submitted?</h4>
                    <p style="margin-top: 0.75rem;">A: Tidak bisa di-cancel. Tapi Anda bisa minta approver untuk reject,
                        atau hubungi admin untuk bantuan khusus jika ada kasus emergency.</p>
                </div>
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
