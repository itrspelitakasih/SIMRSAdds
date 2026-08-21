# Deploy E-Tiket RS ke VPS

Panduan ini untuk deploy aplikasi Laravel 12 "E-Tiket RS" ke VPS Ubuntu
22.04/24.04 dengan Nginx + PHP-FPM, memakai git sebagai metode transfer kode.

## 0. Sebelum mulai

- Domain sudah diarahkan (DNS A record) ke IP VPS.
- Repository git sudah punya remote (GitHub/GitLab/dll). Kalau belum:
  ```bash
  git remote add origin git@github.com:namamu/etiket.git
  git push -u origin main
  ```
- Akses SSH ke VPS dengan user yang punya sudo.

## 1. Setup server (sekali saja)

Clone dulu repo ke lokal server (atau upload script-nya saja), lalu jalankan:

```bash
git clone <url-repo-anda> /var/www/etiket
cd /var/www/etiket
chmod +x deploy/scripts/*.sh
./deploy/scripts/server-setup.sh
```

Script ini menginstall: PHP 8.3 + ekstensi, Composer, Node.js 20, Nginx,
MySQL, Supervisor, Certbot.

## 2. Setup database

```bash
sudo mysql_secure_installation
sudo mysql -u root -p
```

Di prompt MySQL:

```sql
CREATE DATABASE etiket CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'etiket_user'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_KUAT';
GRANT ALL PRIVILEGES ON etiket.* TO 'etiket_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 3. Konfigurasi Nginx

```bash
sudo cp deploy/nginx/etiket.conf /etc/nginx/sites-available/etiket.conf
sudo nano /etc/nginx/sites-available/etiket.conf   # ganti server_name & cek path php-fpm socket
sudo ln -s /etc/nginx/sites-available/etiket.conf /etc/nginx/sites-enabled/etiket.conf
sudo nginx -t && sudo systemctl reload nginx
```

Aktifkan HTTPS:

```bash
sudo certbot --nginx -d your-domain.com
```

## 4. Deploy pertama kali

```bash
cd /var/www/etiket

cp deploy/env.production.example .env
nano .env                       # isi DB_PASSWORD, APP_URL, dll
php artisan key:generate

composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build

php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Jalankan seeder untuk membuat role, permission, kategori, unit, dan akun
admin pertama:

```bash
php artisan db:seed
```

> ⚠️ **Penting**: seeder ini membuat akun admin default
> `admin@rs.local` / `password` (lihat `database/seeders/DatabaseSeeder.php`).
> **Segera login dan ganti passwordnya** setelah aplikasi live — jangan
> biarkan kredensial default ini aktif di produksi.

### Permission

```bash
sudo chown -R www-data:www-data /var/www/etiket
sudo find /var/www/etiket/storage -type d -exec chmod 775 {} \;
sudo find /var/www/etiket/bootstrap/cache -type d -exec chmod 775 {} \;
```

## 5. Queue worker (Supervisor)

Aplikasi mengirim notifikasi WhatsApp lewat queue job, jadi worker WAJIB
jalan di produksi (QUEUE_CONNECTION=database di .env).

```bash
sudo cp deploy/supervisor/etiket-worker.conf /etc/supervisor/conf.d/etiket-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start etiket-worker:*
sudo supervisorctl status etiket-worker:*
```

## 6. Scheduler (cron)

Perintah `documents:send-reminders` (pengingat dokumen jatuh tempo) berjalan
lewat Laravel Scheduler, jadi butuh satu cron entry:

```bash
sudo crontab -e -u www-data
```

Tempel isi dari `deploy/cron/etiket-cron`:

```
* * * * * cd /var/www/etiket && php artisan schedule:run >> /dev/null 2>&1
```

## 7. Konfigurasi dari dalam aplikasi

Dua integrasi dikonfigurasi lewat halaman admin (bukan `.env`), isi setelah
aplikasi live:

- **WhatsApp (Gowa)**: menu admin WhatsApp Settings — isi base URL/port/kredensial
  server Gowa yang dipakai untuk kirim notifikasi tiket baru & selesai.
- **Google Drive**: menu Pengaturan → Google Drive (`/pengaturan/google-drive`)
  — untuk penyimpanan dokumen. Di Google Cloud Console, set Authorized
  redirect URI ke:
  `https://your-domain.com/pengaturan/google-drive/callback`

## 8. Deploy update selanjutnya

Setiap kali ada perubahan kode:

```bash
cd /var/www/etiket
./deploy/scripts/deploy.sh
```

Script ini otomatis: maintenance mode → git pull → composer install →
npm build → migrate → cache ulang → restart queue worker & PHP-FPM →
matikan maintenance mode.

## 9. Checklist keamanan produksi

- [ ] `APP_DEBUG=false` dan `APP_ENV=production`
- [ ] `.env` tidak pernah masuk git (sudah di-ignore)
- [ ] HTTPS aktif (Certbot) + `SESSION_SECURE_COOKIE=true`
- [ ] Firewall (`ufw`) hanya buka port 22, 80, 443
- [ ] Backup database terjadwal (mis. `mysqldump` via cron harian)
- [ ] Password MySQL & kredensial Gowa kuat dan unik
