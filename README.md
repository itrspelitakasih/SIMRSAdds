# E-Tiket RS

**E-Tiket RS** adalah aplikasi tiket helpdesk IT internal untuk rumah sakit,
dibangun dengan **Laravel 12**, **Tailwind CSS v4**, dan **Alpine.js**. Pasien
maupun staf dapat melaporkan kendala/permintaan lewat form publik tanpa login,
lalu tim IT mengelola, menugaskan, dan menyelesaikan tiket dari dashboard
admin — lengkap dengan notifikasi WhatsApp, manajemen dokumen legal dengan
pengingat jatuh tempo, dan laporan.

## ✨ Fitur Utama

- 🎫 **Tiket Publik** — halaman `/lapor` untuk membuat laporan tanpa login,
  dan `/lacak` untuk melacak status tiket memakai kode tiket.
- 🛠️ **Manajemen Tiket (Admin)** — assign ke petugas, ubah status, tambah
  komentar/log per tiket, kategori & unit tujuan.
- 👥 **Role & Permission** — berbasis [spatie/laravel-permission](https://spatie.be/docs/laravel-permission),
  role dan hak akses diatur lewat menu Roles & Users.
- 📄 **Manajemen Dokumen** — dokumen legal/perizinan dengan tanggal jatuh
  tempo, status (ok/warning/critical/expired), dan pengingat otomatis
  (`documents:send-reminders`) via WhatsApp maupun tampilan kalender.
- 💬 **Notifikasi WhatsApp** — terintegrasi dengan server [Gowa](https://github.com/aldinokemal/go-whatsapp-web-multidevice)
  untuk mengirim notifikasi tiket baru/selesai dan pengingat dokumen,
  dikonfigurasi lewat menu admin Pengaturan → WhatsApp.
- ☁️ **Integrasi Google Drive** — penyimpanan dokumen opsional lewat
  `yaza/laravel-google-drive-storage`, dikonfigurasi lewat menu admin
  Pengaturan → Google Drive (OAuth).
- 📊 **Laporan & Export** — halaman Laporan dengan export data (Excel/PDF)
  memakai `maatwebsite/excel` dan `barryvdh/laravel-dompdf`.
- 📅 **Kalender** — menampilkan jadwal jatuh tempo dokumen.
- 🎨 **Dashboard TailAdmin** — UI admin modern berbasis template TailAdmin
  Laravel (Tailwind v4 + Alpine.js + Vite), dark mode, chart, tabel, dsb.

## 📋 Requirements

- **PHP 8.2+**
- **Composer**
- **Node.js 18+** dan **npm**
- **MySQL** (default; bisa disesuaikan ke PostgreSQL/SQLite)
- (Opsional) Server [Gowa](https://github.com/aldinokemal/go-whatsapp-web-multidevice) untuk notifikasi WhatsApp
- (Opsional) Google Cloud OAuth credentials untuk integrasi Google Drive

## 🚀 Instalasi Lokal (Development)

### 1. Clone repository

```bash
git clone https://github.com/itrspelitakasih/SIMRSAdds.git eTiket
cd eTiket
```

### 2. Install dependency

```bash
composer install
npm install
```

### 3. Environment

```bash
cp .env.example .env      # Windows: copy .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=etiket
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & seeder

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

> ⚠️ Seeder membuat akun admin default `admin@rs.local` / `password`
> (lihat `database/seeders/DatabaseSeeder.php`) beserta role, permission,
> kategori, dan unit dasar. **Segera ganti password ini** setelah aplikasi
> berjalan, terutama sebelum dipakai di produksi.

### 5. Jalankan aplikasi

```bash
composer run dev
```

Perintah ini menjalankan sekaligus: server Laravel, queue worker, log
(`pail`), dan Vite dev server. Buka [http://localhost:8000](http://localhost:8000).

Atau jalankan manual di terminal terpisah:

```bash
php artisan serve
php artisan queue:listen
npm run dev
```

### 6. Build untuk produksi

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🧪 Testing

```bash
composer run test
# atau
php artisan test
```

## 📁 Struktur Proyek (ringkas)

```
eTiket/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Ticket, Category, Unit, User, Role, Document,
│   │   │                     WhatsApp & Google Drive settings, Report
│   │   └── Guest/          # Form lapor & lacak tiket publik
│   ├── Models/              # Ticket, Document, Category, Unit, User, dst.
│   └── Console/Commands/    # documents:send-reminders, dll.
├── database/
│   ├── migrations/
│   └── seeders/             # Role, Permission, Category, Unit, admin default
├── deploy/                  # Tooling deploy VPS (Nginx, scripts, cron, supervisor)
├── resources/views/         # Blade templates (dashboard, tiket, pengaturan)
├── routes/web.php
└── ...
```

## ⚙️ Konfigurasi dari Dalam Aplikasi

Beberapa integrasi **tidak** diatur lewat `.env`, tapi lewat menu admin
setelah aplikasi berjalan:

- **WhatsApp (Gowa)** — menu *Pengaturan → WhatsApp*: isi base URL, port, dan
  kredensial server Gowa untuk kirim notifikasi tiket baru/selesai serta
  pengingat dokumen.
- **Google Drive** — menu *Pengaturan → Google Drive* (`/pengaturan/google-drive`):
  hubungkan akun Google untuk penyimpanan dokumen (dokumen di-upload otomatis
  ke Drive saat disimpan, lihat `GoogleDriveService::uploadDocument`).

### Setup Google Drive di Google Cloud Console

Integrasi ini pakai OAuth2 (scope penuh `drive`) supaya aplikasi bisa upload
file ke akun Google Drive yang kamu hubungkan. Berikut langkah membuat
kredensialnya:

1. **Buat/pilih project** di [Google Cloud Console](https://console.cloud.google.com/).
2. **Aktifkan Google Drive API**: menu *APIs & Services → Library*, cari
   **Google Drive API**, klik **Enable**.
3. **Konfigurasi OAuth consent screen**: menu *APIs & Services → OAuth
   consent screen*.
   - User type: **External** (atau **Internal** kalau pakai Google Workspace
     dan hanya untuk internal RS).
   - Isi App name, User support email, Developer contact email.
   - Scopes: tidak wajib ditambah manual di sini (scope `drive` diminta saat
     login lewat kode aplikasi), langsung **Save and Continue**.
   - Test users: kalau consent screen masih status **Testing**, tambahkan
     email Google yang akan dipakai untuk connect (misalnya email admin RS)
     di tab **Test users** — kalau tidak, Google akan menolak login dengan
     error `access_denied`.
4. **Buat OAuth Client ID**: menu *APIs & Services → Credentials* → **Create
   Credentials → OAuth client ID**.
   - Application type: **Web application**.
   - Name: bebas, mis. `E-Tiket RS`.
   - **Authorized redirect URIs**, tambahkan persis (sesuai `APP_URL`):
     - Lokal: `http://localhost:8000/pengaturan/google-drive/callback`
     - Produksi: `https://domain-anda.com/pengaturan/google-drive/callback`
   - Klik **Create** → salin **Client ID** dan **Client Secret** yang muncul.
5. **Isi kredensial di aplikasi**: login ke E-Tiket RS sebagai admin, buka
   *Pengaturan → Google Drive*, tempel **Client ID** dan **Client Secret**,
   (opsional) isi **Folder ID** tujuan upload — ambil dari URL folder Drive
   `https://drive.google.com/drive/folders/<FOLDER_ID>` — lalu **Save**.
6. **Hubungkan akun**: klik tombol **Connect/Hubungkan**. Kamu akan diarahkan
   ke layar consent Google — pilih akun, izinkan akses. Pastikan Google
   mengembalikan **refresh token**; jika tidak (`Google tidak mengembalikan
   refresh token`), buka [myaccount.google.com/permissions](https://myaccount.google.com/permissions),
   cabut akses aplikasi ini, lalu ulangi proses connect (Google hanya
   mengirim refresh token pada otorisasi pertama kali, kecuali dicabut dulu).
7. **Uji koneksi**: klik tombol **Test Connection** di halaman yang sama
   untuk memastikan aplikasi bisa membaca daftar folder di Drive tersebut.

> Catatan: jika consent screen masih status **Testing** (belum di-publish),
> token Google hanya berlaku ~7 hari dan perlu connect ulang secara berkala.
> Untuk penggunaan produksi jangka panjang, publish OAuth consent screen ke
> status **In production** (butuh verifikasi Google jika scope-nya sensitif,
> seperti `drive` penuh).

## ☁️ Deploy ke VPS

Panduan lengkap deploy ke VPS Ubuntu (Nginx + PHP-FPM + MySQL + Supervisor)
ada di [deploy/DEPLOY.md](deploy/DEPLOY.md). Ringkasnya:

1. **Setup server sekali saja** — clone repo ke server lalu jalankan
   `deploy/scripts/server-setup.sh` (install PHP 8.3, Composer, Node.js 20,
   Nginx, MySQL, Supervisor, Certbot).
2. **Buat database** MySQL dan user khusus aplikasi.
3. **Konfigurasi Nginx** dari `deploy/nginx/etiket.conf`, lalu aktifkan HTTPS
   dengan Certbot.
4. **Deploy pertama kali**: copy `deploy/env.production.example` ke `.env`,
   isi kredensial, lalu `composer install --no-dev`, `npm run build`,
   `migrate --force`, `db:seed`, dan cache Laravel.
5. **Queue worker** via Supervisor (`deploy/supervisor/etiket-worker.conf`)
   — wajib jalan karena notifikasi WhatsApp dikirim lewat queue job.
6. **Scheduler** via cron (`deploy/cron/etiket-cron`) untuk pengingat
   dokumen jatuh tempo (`documents:send-reminders`).
7. **Deploy update berikutnya** cukup jalankan `./deploy/scripts/deploy.sh`
   — otomatis maintenance mode → git pull → install deps → build →
   migrate → cache ulang → restart worker & PHP-FPM.

Isi direktori `deploy/`:

```
deploy/
├── DEPLOY.md                  # Panduan deploy lengkap (step-by-step)
├── env.production.example     # Contoh .env untuk produksi
├── nginx/etiket.conf           # Virtual host Nginx + PHP-FPM
├── scripts/
│   ├── server-setup.sh        # Provisioning server (sekali jalan)
│   └── deploy.sh               # Script deploy/update aplikasi
├── supervisor/etiket-worker.conf  # Konfigurasi queue worker
└── cron/etiket-cron            # Entry cron untuk Laravel Scheduler
```

Checklist keamanan produksi (detail di DEPLOY.md): `APP_DEBUG=false`,
HTTPS aktif, firewall hanya buka port 22/80/443, backup database terjadwal,
serta ganti password admin default dan kredensial Gowa/MySQL.

## 🐛 Troubleshooting

```bash
# Class not found
composer dump-autoload

# Permission error di storage/bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Reset semua cache
php artisan optimize:clear
```

## License

Base template UI menggunakan [TailAdmin Laravel](https://tailadmin.com/laravel) — lihat [LICENSE](LICENSE).
