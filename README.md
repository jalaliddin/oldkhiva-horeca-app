# OldKhiva.uz — B2B Restaurant Platform

Tourist companies (B2B clients) can register, view menus, create bookings, and manage invoices/payments.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.3, MySQL 8 |
| Frontend | Vue 3, Vuetify 3, Pinia, Vite |
| Auth | Laravel Sanctum (token-based) |
| PDF | barryvdh/laravel-dompdf |
| Deploy | Docker, Nginx |

---

## Project Structure

```
oldkhiva.uz/
├── backend/                  # Laravel 13 API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── Admin/        # Admin endpoints
│   │   │   └── ...           # Client endpoints
│   │   ├── Models/           # Eloquent models
│   │   └── Http/Middleware/  # AdminMiddleware, VerifiedClientMiddleware
│   ├── routes/api.php
│   ├── Dockerfile
│   └── docker-entrypoint.sh
├── frontend/                 # Vue 3 SPA
│   ├── src/
│   │   ├── views/
│   │   │   ├── admin/        # Admin panel pages
│   │   │   ├── client/       # Client panel pages
│   │   │   ├── auth/         # Login, Register
│   │   │   └── landing/      # Landing page
│   │   ├── layouts/          # AdminLayout, ClientLayout
│   │   ├── stores/           # Pinia stores (auth, notification)
│   │   ├── plugins/axios.js  # Axios instance with interceptors
│   │   └── router/index.js
├── nginx/
│   ├── Dockerfile            # Multi-stage: builds Vue + serves nginx
│   ├── default.conf          # Docker nginx config
│   └── vps-nginx.conf        # VPS system nginx config (SSL + proxy)
├── docker-compose.yml
├── .env.docker               # Production environment variables
└── Makefile                  # Convenience commands
```

---

## Roles

| Role | Access |
|---|---|
| `admin` | Full access — clients, bookings, invoices, payments, menu, reports, landing settings |
| `client` | Must agree to contract → can browse menu, create bookings, view invoices/payments |

**Default credentials (after seeding):**
- Admin: `admin@oldkhiva.uz` / `Admin@1234`
- Demo client: `client@demo.com` / `Client@1234`

---

## Local Development

### Requirements
- PHP 8.3, Composer
- Node.js 20+
- MySQL 8

### Setup

```bash
# 1. Backend
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php -d upload_max_filesize=20M -d post_max_size=25M artisan serve
# Runs on http://localhost:8000

# 2. Frontend (separate terminal)
cd frontend
npm install
npm run dev
# Runs on http://localhost:5173
```

---

## Docker Deployment (Production)

### Requirements
- Docker Engine 24+
- Docker Compose v2

### First deploy

```bash
# 1. Clone the repo
git clone https://github.com/your-repo/oldkhiva.uz.git
cd oldkhiva.uz

# 2. Edit environment variables
nano .env.docker

# 3. Build and start
make restart

# 4. Seed initial data (first time only)
make seed
```

### Update deploy

```bash
git pull
make restart
```

---

## VPS Nginx Setup (SSL + Reverse Proxy)

Docker runs on port `8091`. VPS system nginx proxies to it.

```bash
# 1. Install nginx + certbot
apt install nginx certbot python3-certbot-nginx -y

# 2. Copy config
cp nginx/vps-nginx.conf /etc/nginx/sites-available/oldkhiva.uz
ln -s /etc/nginx/sites-available/oldkhiva.uz /etc/nginx/sites-enabled/

# 3. Test and reload
nginx -t && systemctl reload nginx

# 4. Issue SSL certificate
certbot --nginx -d oldkhiva.uz -d www.oldkhiva.uz
```

**Traffic flow:**
```
Internet :80/:443
    ↓
VPS Nginx (SSL termination)
    ↓ proxy_pass http://127.0.0.1:8091
Docker Nginx :8091
    ├── /          → Vue 3 SPA (static)
    ├── /storage/  → Uploaded files
    └── /api/      → PHP-FPM :9000 (Laravel)
                         ↓
                       MySQL
```

---

## Makefile Commands

```bash
make restart      # Build images and start containers
make up           # Start without rebuilding
make down         # Stop containers
make logs         # Tail all logs
make logs-app     # Tail PHP-FPM logs only
make logs-nginx   # Tail nginx logs only
make shell        # Open shell inside app container
make migrate      # Run database migrations
make seed         # Run database seeders
make cache-clear  # Clear all Laravel caches
make ps           # Show container status

# Run any artisan command:
make artisan cmd="route:list"
make artisan cmd="tinker"
```

---

## API Overview

### Public
| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/auth/register` | Register new client |
| POST | `/api/auth/login` | Login |
| GET | `/api/landing` | Landing page data |

### Client (requires token + contract agreed)
| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/menu` | Menu categories & items |
| GET | `/api/services` | Available services |
| POST | `/api/bookings` | Create booking |
| GET | `/api/invoices` | My invoices |
| GET | `/api/invoices/{id}/download` | Download invoice PDF |
| GET | `/api/payments` | My payment history |
| GET | `/api/deposit` | My deposit balance |

### Admin (requires admin role)
| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/admin/dashboard` | Stats overview |
| GET/PUT | `/api/admin/clients` | Manage clients |
| POST | `/api/admin/bookings/{id}/approve` | Approve booking → creates invoice |
| GET | `/api/admin/invoices/{id}/download` | Download invoice PDF |
| POST | `/api/admin/payments` | Record payment |
| GET | `/api/admin/reports/clients` | Client financial report |
| GET | `/api/admin/reports/invoices` | Invoice report |
| POST | `/api/admin/landing-settings/upload-image` | Upload hero/about image |

---

## Environment Variables (.env.docker)

```env
APP_KEY=          # Generate: php artisan key:generate --show
APP_URL=          # https://oldkhiva.uz
DB_PASSWORD=      # Strong password for production
SANCTUM_STATEFUL_DOMAINS=oldkhiva.uz
SESSION_DOMAIN=.oldkhiva.uz
```

---

## Models

```
User (client/admin)
 └── Deposit          (1:1)
 └── Booking          (1:N)
     └── BookingItem  (1:N) → MenuItem or Service (polymorphic)
     └── Invoice       (1:1)
         └── Payment  (1:N)
```

Auto-generated numbers:
- Bookings: `BRN-YYYY-NNNN`
- Invoices: `INV-YYYY-NNNN`
- Payments: `PAY-YYYY-NNNN`
