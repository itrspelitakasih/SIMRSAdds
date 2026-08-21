#!/usr/bin/env bash
# Deploy/update E-Tiket RS di VPS. Jalankan dari dalam /var/www/etiket
# setiap kali ada perubahan kode yang perlu ditarik (git pull) dan diterapkan.
#
#   cd /var/www/etiket
#   ./deploy/scripts/deploy.sh
#
# Catatan: script ini TIDAK menjalankan "server-setup.sh" (itu hanya sekali
# di awal) dan tidak membuat file .env (itu juga hanya sekali di awal).

set -euo pipefail

APP_DIR="/var/www/etiket"
cd "$APP_DIR"

echo "==> Aktifkan mode maintenance"
php artisan down --retry=15 || true

echo "==> Tarik kode terbaru dari git"
git pull origin main

echo "==> Install dependency PHP (production, tanpa dev)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Install dependency Node & build asset frontend"
npm ci
npm run build

echo "==> Jalankan migrasi database"
php artisan migrate --force

echo "==> Pastikan symlink storage ada"
php artisan storage:link || true

echo "==> Bersihkan & cache ulang config/route/view"
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> Restart queue worker (Supervisor)"
sudo supervisorctl restart etiket-worker:* || echo "   (lewati: supervisor belum dipasang / worker belum terdaftar)"

echo "==> Restart PHP-FPM"
sudo systemctl restart php8.3-fpm

echo "==> Matikan mode maintenance"
php artisan up

echo ""
echo "Deploy selesai."
