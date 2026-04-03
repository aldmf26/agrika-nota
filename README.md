# Sistem Pencatatan Nota Manual

Aplikasi web internal untuk mencatat, memverifikasi, dan merekap nota pengeluaran manual dari berbagai divisi/lokasi. Dibangun dengan **Laravel 11 + Breeze + Spatie Permission**.

---

## Daftar Isi

- [Gambaran Sistem](#gambaran-sistem)
- [Teknologi](#teknologi)
- [Struktur Role & Permission](#struktur-role--permission)
- [Alur Kerja](#alur-kerja)
- [Tipe Nota](#tipe-nota)
- [Field & Form Input](#field--form-input)
- [Aturan Validasi](#aturan-validasi)
- [Struktur Database](#struktur-database)
- [Instalasi](#instalasi)
- [Konfigurasi Awal](#konfigurasi-awal)
- [Struktur Folder](#struktur-folder)

---

## Gambaran Sistem

Sistem ini menggantikan alur pencatatan nota via grup WhatsApp. Admin menginput nota beserta foto lampirannya melalui form web, lalu IT Approver melakukan verifikasi sebelum data masuk ke rekap laporan.

**Masalah yang diselesaikan:**
- Foto nota blur/tidak terbaca → validasi wajib ada lampiran
- Nomor nota dobel → cek unik otomatis
- Keterangan tidak standar → teks bebas, fleksibel
- Split tagihan ke banyak divisi sulit dicatat → form khusus split
- Kelebihan bayar hilang dari catatan → deposit log otomatis

---

## Teknologi

| Komponen | Pilihan |
|---|---|
| Framework | Laravel 11 |
| Auth & UI starter | Laravel Breeze (Blade + Vite) |
| Role & Permission | Spatie Laravel Permission |
| Database | MySQL 8 |
| Upload file | Laravel Storage (local / S3) |
| Export Excel | Maatwebsite/Laravel-Excel |
| Notifikasi | Laravel Notifications (mail / database) |

---

## Struktur Role & Permission

### Role

| Role | Deskripsi |
|---|---|
| `admin` | Input nota, lihat nota milik sendiri, edit draft |
| `approver` | Review semua nota, approve/reject, tambah catatan |
| `super_admin` | Semua akses + kelola user + kelola divisi/lokasi |

### Permission (Spatie)

```
nota.create
nota.edit-own
nota.view-own
nota.view-all
nota.approve
nota.reject
nota.void
nota.export
user.manage
divisi.manage
```

> `admin` mendapat: `nota.create`, `nota.edit-own`, `nota.view-own`  
> `approver` mendapat: semua `nota.*` kecuali `nota.create`  
> `super_admin` mendapat: semua permission

### Seeder Role & Permission

```bash
php artisan db:seed --class=RolePermissionSeeder
```

---

## Alur Kerja

```
Admin ──► Input Nota ──► Submit ──► [Pending Review]
                                          │
                          ┌───────────────┴────────────────┐
                          ▼                                 ▼
                    [Approver: OK]                  [Approver: Reject]
                          │                                 │
                    [Approved]                    Notif ke Admin ──► Admin Revisi
                          │
              ┌───────────┼───────────┐
              ▼           ▼           ▼
         Rekap Laporan  Deposit Log  Export Excel
```

### Status Nota

| Status | Keterangan |
|---|---|
| `draft` | Disimpan tapi belum disubmit |
| `pending` | Sudah disubmit, menunggu review |
| `approved` | Disetujui approver |
| `rejected` | Ditolak, admin perlu revisi |
| `void` | Dibatalkan/tidak terpakai, data tetap tersimpan |

> **Catatan:** Nota yang void tidak dihapus dari database. Status `void` ditandai dan tidak masuk ke rekap laporan.

---

## Tipe Nota

### 1. Biasa
Nota pembelian/pengeluaran standar. Satu divisi, satu nominal.

**Contoh:** upah harian, uang makan supir, biaya armada, beli barang.

### 2. Split Tagihan
Satu tagihan dibagi ke beberapa divisi. Sistem menghitung subtotal otomatis.

**Contoh:** tagihan internet Indihome dibagi ke Aga, Agri Cost, CLS Q, LL Q.

> Admin cukup input nominal per divisi. Sistem jumlahkan sendiri.

### 3. Revenue Sharing
Perhitungan persentase dari base amount. Nominal dihitung otomatis dari rumus.

**Contoh:** `587.487.136 × 8% = 46.998.971`

> Admin input: base amount + persentase. Total muncul otomatis di preview sebelum submit.

### 4. Kelebihan Bayar (Deposit)
Selisih lebih dari yang seharusnya dibayar. Otomatis masuk ke deposit log dan akan dipotong di transaksi berikutnya dengan supplier yang sama.

**Contoh:** Agri Cost bayar 25.550.000, nota 25.375.000 → deposit 175.000.

### 5. Nota Digital / Screenshot
Nota dari aplikasi (transfer bank, marketplace, dll) tanpa nomor nota fisik. Wajib attach screenshot sebagai lampiran.

---

## Field & Form Input

### Field Dasar (semua tipe)

| Field | Tipe Input | Wajib | Keterangan |
|---|---|---|---|
| Bulan | Select (Jan–Des + tahun) | Ya | Auto-fill bulan saat ini |
| Tanggal | Date picker | Ya | — |
| Lokasi / Divisi | Select | Ya | Daftar dari master divisi |
| No. Nota | Text | Ya* | Unik per sistem. Kosongkan jika tidak ada nomor fisik |
| Keterangan | Textarea (teks bebas) | Ya | Tidak ada dropdown, bebas diisi |
| Nominal (Rp) | Number | Ya | Format rupiah, otomatis ribuan |
| Foto Lampiran | File upload | Ya | Min 1 foto, format JPG/PNG, max 5MB |
| Nama Pembuat | Auto (user login) | — | Tidak perlu diisi manual |

> `*` No. nota dikosongkan = sistem generate kode internal. Jika diisi, sistem cek apakah sudah dipakai.

### Field Tambahan per Tipe

**Split Tagihan:**
- Tambah divisi → input nominal masing-masing
- Tombol "+ Tambah Divisi" bisa diklik berkali-kali
- Subtotal dihitung real-time

**Revenue Sharing:**
- Base amount (Rp)
- Persentase (%)
- Total otomatis terhitung: `base × persen / 100`

**Kelebihan Bayar:**
- Nominal nota (seharusnya dibayar)
- Nominal dibayar (aktual)
- Selisih otomatis terhitung → masuk deposit log

### Prinsip UX Form

- Sesimpan mungkin — hanya tampilkan field yang relevan dengan tipe nota
- Tidak ada dropdown untuk keterangan — admin bebas tulis apa saja
- Preview nominal sebelum submit
- Tombol "Simpan Draft" tersedia agar bisa dilanjut nanti
- Mobile-friendly — admin sering input dari HP

---

## Aturan Validasi

| Aturan | Detail |
|---|---|
| Foto wajib | Minimal 1 lampiran, tidak bisa submit tanpa foto |
| No. nota unik | Sistem cek ke database. Jika sudah ada → warning, minta konfirmasi |
| Nominal tidak nol | Tidak bisa submit jika nominal 0 atau kosong |
| Keterangan tidak kosong | Wajib diisi, minimal 5 karakter |
| Format file | Hanya JPG, PNG, HEIC. Max 5MB per file |
| Split subtotal | Harus > 0, masing-masing divisi minimal diisi satu |

---

## Struktur Database

### Tabel Utama

```
nota
├── id
├── kode_internal          -- auto-generate jika no_nota kosong
├── no_nota                -- nomor dari nota fisik (nullable)
├── tipe                   -- biasa | split | revenue_sharing | deposit | digital
├── status                 -- draft | pending | approved | rejected | void
├── bulan
├── tanggal
├── divisi_id              -- FK ke tabel divisi
├── keterangan             -- teks bebas
├── nominal_total
├── user_id                -- admin yang buat (FK ke users)
├── approved_by            -- approver (FK ke users, nullable)
├── approved_at
├── catatan_reject         -- catatan dari approver jika reject
├── created_at
└── updated_at

nota_split_items           -- untuk tipe split
├── id
├── nota_id
├── divisi_id
└── nominal

nota_revenue_sharing       -- untuk tipe revenue sharing
├── id
├── nota_id
├── base_amount
├── persentase
└── nominal_hasil

deposit_log                -- untuk kelebihan bayar
├── id
├── nota_id
├── divisi_id
├── nominal_deposit
├── sisa_deposit           -- berkurang tiap ada pemotongan
├── status                 -- aktif | lunas
└── created_at

nota_attachments           -- foto/lampiran
├── id
├── nota_id
├── file_path
├── file_name
└── uploaded_at

divisi                     -- master lokasi/divisi
├── id
├── nama                   -- Aga, Agri Cost, Orchard, dll
├── kode
└── aktif
```

---

## Instalasi

### Prasyarat

- PHP 8.2+
- Composer
- Node.js 18+ & NPM
- MySQL 8

### Langkah Instalasi

```bash
# 1. Clone repo
git clone https://github.com/yourorg/nota-manual.git
cd nota-manual

# 2. Install dependency PHP
composer install

# 3. Install dependency JS
npm install

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Edit .env (database, mail, storage)
# DB_DATABASE=nota_manual
# DB_USERNAME=...
# DB_PASSWORD=...

# 6. Migrate & seed
php artisan migrate --seed

# 7. Storage link
php artisan storage:link

# 8. Build assets
npm run build

# 9. Jalankan server
php artisan serve
```

---

## Konfigurasi Awal

Setelah instalasi, login sebagai `super_admin` dan lakukan:

1. **Kelola Divisi** → `/admin/divisi` → tambah semua lokasi (Aga, Agri Cost, Orchard, SDB, Takemori, Soondobu, CLS, LL Pribadi, Linda Pribadi, dll)
2. **Kelola User** → `/admin/users` → buat akun untuk Andri, Nanda, Aldi dengan role `admin`, dan approver dengan role `approver`
3. **Cek Permission** → pastikan seeder berjalan dengan benar via `php artisan permission:show`

### Akun Default Seeder

| Email | Password | Role |
|---|---|---|
| superadmin@nota.local | password | super_admin |
| approver@nota.local | password | approver |

> Ganti password setelah login pertama.

---

## Struktur Folder

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── NotaController.php          -- CRUD nota
│   │   ├── NotaApprovalController.php  -- approve/reject
│   │   ├── DepositController.php       -- kelola deposit log
│   │   └── Admin/
│   │       ├── UserController.php
│   │       └── DivisiController.php
│   ├── Requests/
│   │   └── StoreNotaRequest.php        -- validasi form
│   └── Middleware/
├── Models/
│   ├── Nota.php
│   ├── NotaSplitItem.php
│   ├── NotaRevenueSharing.php
│   ├── DepositLog.php
│   ├── NotaAttachment.php
│   └── Divisi.php
├── Exports/
│   └── NotaExport.php                  -- Maatwebsite Excel
└── Notifications/
    ├── NotaPendingNotification.php     -- notif ke approver
    └── NotaRejectedNotification.php   -- notif ke admin

resources/views/
├── nota/
│   ├── index.blade.php                 -- daftar nota
│   ├── create.blade.php                -- form input
│   ├── show.blade.php                  -- detail nota
│   └── partials/
│       ├── form-biasa.blade.php
│       ├── form-split.blade.php
│       └── form-revenue.blade.php
├── approval/
│   ├── index.blade.php                 -- antrian pending
│   └── show.blade.php                  -- detail + tombol approve/reject
└── admin/
    ├── users/
    └── divisi/

database/
├── migrations/
└── seeders/
    ├── RolePermissionSeeder.php
    ├── DivisiSeeder.php
    └── UserSeeder.php
```

---

## Catatan Pengembangan

- **Keterangan** → selalu textarea bebas. Jangan dijadikan dropdown meskipun datanya berulang. Fleksibilitas lebih penting dari konsistensi di field ini.
- **No. Nota** → jika admin tidak punya nomor fisik (nota digital/screenshot), biarkan kosong. Sistem generate kode internal format `INT-YYYYMM-XXXX`.
- **Void** → tidak boleh hapus nota dari database. Set status ke `void`, tambahkan alasan, dan kecualikan dari semua laporan.
- **Split** → subtotal dihitung di frontend (real-time) dan divalidasi ulang di backend sebelum simpan.
- **Deposit** → setiap kali ada nota baru untuk divisi yang sama, sistem cek apakah ada deposit aktif dan tampilkan ke admin sebagai informasi sebelum submit.
- **Foto** → simpan di `storage/app/private/nota/` agar tidak bisa diakses publik langsung. Akses via signed URL.

---

*Dokumentasi ini dibuat berdasarkan kebutuhan aktual dari alur grup WhatsApp "Pengecekan Nota Manual" — Maret 2026.*