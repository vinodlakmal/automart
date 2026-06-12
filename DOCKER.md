# Running with Docker (isolated — safe alongside your other sites)

This runs the whole app (PHP 8.2, Nginx, MySQL 8) inside containers. It does **not**
use or change the host's PHP 8.1, the live site's Nginx, or the host MySQL. The site
is served on host port **8080**; the database is container-only (no host port).

## On the VM

### 1. Install Docker (one time, if not already present)

```bash
docker --version || curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER     # so you can run docker without sudo
# log out and back in (or run: newgrp docker) for the group change to apply
```

> Installing Docker does not affect your existing sites — they keep running on the host.

### 2. Configure .env for Docker

```bash
cd /var/www/ikman-clone
cp .env.example .env   # if you don't already have one
nano .env
```

Set these (note **DB_HOST=db** — the container name, not 127.0.0.1):

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=http://173.249.28.120:8080

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ikman
DB_USERNAME=ikman
DB_PASSWORD=ChangeThisStrongPass
DB_ROOT_PASSWORD=ChangeThisRootPass
```

`docker compose` reads the same `.env`, so the MySQL container is created with exactly
these credentials automatically.

### 3. Build and start

```bash
docker compose up -d --build
```

First run takes a few minutes (it builds PHP, pulls MySQL/Nginx, runs `composer install`,
generates the key, and migrates automatically via the entrypoint).

### 4. Seed sample data (one time)

```bash
docker compose exec app php artisan db:seed --force
```

### 5. Open it

**http://173.249.28.120:8080**

(Make sure port 8080 is open in your VM provider's firewall/security group.)

---

## Everyday commands

| Action | Command |
|--------|---------|
| Start | `docker compose up -d` |
| Stop | `docker compose down` |
| Rebuild after code pull | `git pull && docker compose up -d --build` |
| Run artisan | `docker compose exec app php artisan <cmd>` |
| Tail app logs | `docker compose logs -f app` |
| Open a shell | `docker compose exec app bash` |
| DB shell | `docker compose exec db mysql -uikman -p ikman` |

## Updating after you push new code from your PC

```bash
cd /var/www/ikman-clone
git pull
docker compose up -d --build
docker compose exec app php artisan migrate --force
```

## Data safety

- MySQL data lives in the named volume `db-data` — it survives `docker compose down`
  and rebuilds. It is **only** deleted if you run `docker compose down -v`.
- Uploaded ad images persist on the host at `public/ads/` (bind-mounted).

## Optional: serve on a subdomain over port 80 later

Keep the container on 8080 and add a small reverse-proxy server block to the **host**
Nginx (the one already serving your live site) — this is the only host change, and it
only adds a new server block, leaving existing ones intact:

```nginx
server {
    listen 80;
    server_name automart.merkeisolutions.com;
    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Point the subdomain's DNS A record at `173.249.28.120` first, then `sudo nginx -t && sudo systemctl reload nginx`.
