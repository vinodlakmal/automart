# Deployment runbook — ikman-clone

Local machine: **Windows** · VM: **Ubuntu 22.04** · Serving: **IP only (no SSL yet)** · GitHub repo: **public**

Fill in these placeholders before you start:

| Placeholder | Your value |
|-------------|------------|
| `<GITHUB_USERNAME>` | your GitHub username |
| `173.249.28.120` | the public IP of your Ubuntu server |
| `merkei_store` | the user you SSH in as (e.g. `ubuntu`, `root`) |

---

## Step 0 — Push the code to GitHub (on Windows)

Open **PowerShell** in the project folder and run:

```powershell
git init
git add .
git commit -m "Initial commit: Laravel classifieds app"
git branch -M main
git remote add origin https://github.com/<GITHUB_USERNAME>/ikman-clone.git
git push -u origin main
```

If `git` is missing, install **Git for Windows**: https://git-scm.com/download/win
(Don't have the repo yet? Create an empty one at https://github.com/new named
`ikman-clone`, public, with no README/.gitignore — then run the commands above.)

---

## Step 1 — Connect to the VM

```powershell
ssh merkei_store@173.249.28.120
```

## Step 2 — Provision the server (one time)

```bash
# copy the script over, or paste its contents; it lives in deploy/server-setup.sh
curl -fsSL https://raw.githubusercontent.com/<GITHUB_USERNAME>/ikman-clone/main/deploy/server-setup.sh -o server-setup.sh
bash server-setup.sh
```

This installs PHP 8.2, Nginx, MySQL, Composer and Node.js 20.

## Step 3 — Create the database

```bash
sudo mysql_secure_installation        # set a root password, answer the prompts
sudo mysql <<'SQL'
CREATE DATABASE ikman CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ikman'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG';
GRANT ALL PRIVILEGES ON ikman.* TO 'ikman'@'localhost';
FLUSH PRIVILEGES;
SQL
```

## Step 4 — Clone the repo

```bash
sudo mkdir -p /var/www
sudo git clone https://github.com/<GITHUB_USERNAME>/ikman-clone.git /var/www/ikman-clone
sudo chown -R $USER:$USER /var/www/ikman-clone
cd /var/www/ikman-clone
```

## Step 5 — Configure `.env` for production

```bash
cp .env.example .env
nano .env
```

Set:

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=http://173.249.28.120

DB_DATABASE=ikman
DB_USERNAME=ikman
DB_PASSWORD=CHANGE_ME_STRONG
```

Then generate the app key:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
```

## Step 6 — First deploy

```bash
./deploy/deploy.sh
php artisan db:seed --force     # one time: load districts/cities/categories
```

## Step 7 — Configure Nginx

```bash
sudo cp deploy/ikman-clone.conf /etc/nginx/sites-available/ikman-clone
# server_name is already set to 173.249.28.120 in the file
sudo ln -s /etc/nginx/sites-available/ikman-clone /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

Set permissions so the web server can write storage and uploaded images:

```bash
sudo chown -R www-data:www-data /var/www/ikman-clone/storage \
     /var/www/ikman-clone/bootstrap/cache /var/www/ikman-clone/public/ads
```

Visit **http://173.249.28.120** — the site should load.

---

## Updating later (one command)

After pushing changes to GitHub from Windows, on the VM:

```bash
cd /var/www/ikman-clone && ./deploy/deploy.sh
```

## Adding HTTPS later (when you have a domain)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```
(First point the domain's A record at `173.249.28.120` and set it as `server_name`.)

---

## Troubleshooting

- **500 / blank page** → `tail -f storage/logs/laravel.log`; check `.env` DB creds and that `APP_KEY` is set.
- **Permission denied writing images** → re-run the `chown www-data` command in Step 7.
- **413 Request Entity Too Large** → `client_max_body_size` in the Nginx config (already 50M).
- **Images 404** → run `php artisan storage:link`; confirm `public/ads` exists and is writable.
- **Full-text search empty on MySQL** → ensure the table is InnoDB (default) and re-run migrations.
