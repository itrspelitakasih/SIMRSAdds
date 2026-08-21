#!/usr/bin/env bash
# Setup awal VPS Ubuntu 22.04/24.04 untuk E-Tiket RS (Laravel 12).
# Jalankan SEKALI di server baru, sebagai user dengan akses sudo:
#
#   chmod +x deploy/scripts/server-setup.sh
#   ./deploy/scripts/server-setup.sh
#
# Setelah ini selesai, lanjutkan dengan langkah "Deploy pertama kali"
# di deploy/DEPLOY.md.

set -euo pipefail

echo "==> Update paket sistem"
sudo apt update && sudo apt upgrade -y

echo "==> Install paket dasar"
sudo apt install -y software-properties-common curl git unzip supervisor nginx

echo "==> Tambah repo PHP (ondrej/php) untuk PHP 8.3"
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

echo "==> Install PHP 8.3 + ekstensi yang dibutuhkan"
sudo apt install -y php8.3-fpm php8.3-cli php8.3-common php8.3-mysql \
    php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath \
    php8.3-gd php8.3-intl php8.3-soap php8.3-readline

echo "==> Install Composer"
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi
composer --version

echo "==> Install Node.js 20.x (via NodeSource)"
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
    sudo apt install -y nodejs
fi
node -v
npm -v

echo "==> Install MySQL Server"
sudo apt install -y mysql-server
echo "    Jalankan 'sudo mysql_secure_installation' untuk mengamankan MySQL,"
echo "    lalu buat database & user untuk aplikasi (lihat deploy/DEPLOY.md)."

echo "==> Install Certbot (untuk SSL Let's Encrypt)"
sudo apt install -y certbot python3-certbot-nginx

echo "==> Buat direktori aplikasi"
sudo mkdir -p /var/www/etiket
sudo chown -R "$USER":"$USER" /var/www/etiket

echo ""
echo "Setup server selesai. Langkah selanjutnya:"
echo "  1. Clone repository ke /var/www/etiket"
echo "  2. Ikuti deploy/DEPLOY.md bagian 'Deploy pertama kali'"
