# ikman-clone

A classified-ads website (ikman.lk style) built with **Laravel 12 / PHP 8.2**.

Features: ad posting with drag-and-drop image upload (max 8, 5MB each), cascading
District → City dropdowns, dynamic category attributes (vehicles / electronics /
property), full-text search, category & price filters, image gallery, favourites,
buyer↔seller messaging schema, reports and promotions.

---

## 1. Run it locally (Windows)

You need **PHP 8.2+, Composer, MySQL and Node.js**. The easiest way on Windows is to
install **[Laragon](https://laragon.org/download/)** (bundles PHP, MySQL, Nginx) or
use [XAMPP](https://www.apachefriends.org/) + [Composer](https://getcomposer.org/Composer-Setup.exe)
+ [Node.js](https://nodejs.org/).

```powershell
# from the project folder (E:\New folder\Web-site)
composer install
npm install

copy .env.example .env
php artisan key:generate
```

Create a MySQL database called `ikman` (e.g. in phpMyAdmin or HeidiSQL), then set your
DB credentials in `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

```powershell
php artisan migrate --seed     # tables + sample districts/cities/categories
php artisan storage:link

# OPTIONAL but recommended: install auth (login/register) scaffolding
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
php artisan migrate
```

Run it:

```powershell
php artisan serve        # http://127.0.0.1:8000
# in a second terminal, for live asset rebuilds:
npm run dev
```

Demo login (after seeding): **demo@ikman.test / password**

> Note: the Blade layout loads Tailwind from a CDN so the UI renders without a build
> step. For production, run `npm run build` and swap the CDN `<script>` in
> `resources/views/layouts/app.blade.php` for `@vite([...])`.

---

## 2. Put it on GitHub

The repo is **public** and named **ikman-clone**. From the project folder:

```powershell
git init
git add .
git commit -m "Initial commit: Laravel classifieds app"
git branch -M main

# Option A — using GitHub CLI (https://cli.github.com), creates the repo for you:
gh repo create ikman-clone --public --source=. --remote=origin --push

# Option B — create the empty repo first at https://github.com/new
#            (name: ikman-clone, do NOT add a README/.gitignore), then:
git remote add origin https://github.com/<GITHUB_USERNAME>/ikman-clone.git
git push -u origin main
```

`vendor/`, `node_modules/`, `.env` and uploaded images are already git-ignored.

---

## 3. Deploy to the Ubuntu 22.04 VM

See **[DEPLOYMENT.md](DEPLOYMENT.md)** for the full step-by-step runbook. Quick version:

```bash
# on the VM, one time:
bash deploy/server-setup.sh          # installs PHP/Nginx/MySQL/Composer/Node
sudo git clone https://github.com/<GITHUB_USERNAME>/ikman-clone.git /var/www/ikman-clone

# configure .env (production), then:
cd /var/www/ikman-clone && ./deploy/deploy.sh
```

Nginx config lives in `deploy/ikman-clone.conf`.

---

## Project structure

| Path | What |
|------|------|
| `app/Models` | Ad, AdImage, AdAttribute, Category, District, City, Conversation, Message, Favorite, Report, Promotion, User |
| `app/Http/Controllers/AdController.php` | CRUD + `myAds`, `getSubcategories`, `getCities` |
| `app/Http/Requests/StoreAdRequest.php` | Validation with Sinhala messages |
| `app/Policies/AdPolicy.php` | Owner-only update/delete |
| `database/migrations` | 13 domain tables + Laravel base tables, FKs, indexes, full-text on `ads` |
| `database/seeders` | Districts, cities, category tree |
| `resources/views/ads` | `create`, `index`, `show`, `my-ads` |
| `routes/web.php` | resource routes + AJAX endpoints |
| `deploy/` | `server-setup.sh`, `deploy.sh`, `ikman-clone.conf` |
