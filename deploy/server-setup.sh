#!/usr/bin/env bash
#
# One-time provisioning for Ubuntu 22.04.
# Installs PHP 8.2, Nginx, MySQL, Composer and Node.js 20.
# Run on the VM as a sudo user:  bash server-setup.sh
#
set -euo pipefail

echo ">>> Updating apt..."
sudo apt update && sudo apt upgrade -y

echo ">>> Base tools..."
sudo apt install -y software-properties-common curl git unzip zip

echo ">>> PHP 8.2 (ondrej PPA)..."
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y \
  php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml \
  php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl

echo ">>> Nginx..."
sudo apt install -y nginx

echo ">>> MySQL Server..."
sudo apt install -y mysql-server

echo ">>> Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

echo ">>> Node.js 20 + npm..."
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

echo ">>> Enable services..."
sudo systemctl enable --now php8.2-fpm nginx mysql

echo
echo ">>> Done. Versions:"
php -v | head -1; nginx -v; mysql --version; composer --version; node -v
echo
echo "NEXT: secure MySQL and create the database/user:"
echo "  sudo mysql_secure_installation"
echo "  sudo mysql -e \"CREATE DATABASE ikman CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\""
echo "  sudo mysql -e \"CREATE USER 'ikman'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG';\""
echo "  sudo mysql -e \"GRANT ALL PRIVILEGES ON ikman.* TO 'ikman'@'localhost'; FLUSH PRIVILEGES;\""
