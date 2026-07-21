#!/bin/bash

# ============================================================
# NotEDs Simulation - Deployment Script
# ============================================================
# chmod +x deploy.sh
# Usage: bash deploy.sh
#
# Script ini akan:
# 0. Pre-flight checks (PHP extensions, disk space, etc.)
# 1. Pull latest code dari git
# 2. Install dependencies (composer & npm)
# 3. Run migration
# 4. Build frontend assets
# 5. Restart queue worker
# 6. Clear & rebuild cache
# ============================================================

# Exit on error — tapi setiap critical step punya handler sendiri
set -e
trap 'echo -e "${RED}Deploy GAGAL di baris $LINENO!${NC}"; exit 1' ERR

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Error tracking
DEPLOY_ERRORS=0
DEPLOY_WARNINGS=0

# Helper functions
error_exit() {
    echo -e "${RED}FATAL: $1${NC}"
    echo -e "${RED}Deployment dihentikan. Perbaiki error di atas lalu jalankan ulang.${NC}"
    exit 1
}

warn() {
    echo -e "${YELLOW}WARNING: $1${NC}"
    DEPLOY_WARNINGS=$((DEPLOY_WARNINGS + 1))
}

info() {
    echo -e "${BLUE}$1${NC}"
}

success() {
    echo -e "${GREEN}$1${NC}"
}

# Allow composer to run as root (common on VPS)
export COMPOSER_ALLOW_SUPERUSER=1

# ============================================================
# Auto-detect PHP binary (aaPanel / custom PHP builds)
# ============================================================
# Detect custom PHP binary paths (aaPanel, custom builds, etc.)
PHP_BIN=""
for candidate in \
    /www/server/php/84/bin/php \
    /www/server/php/83/bin/php \
    /www/server/php/82/bin/php \
    /usr/local/bin/php \
    /usr/bin/php; do
    if [ -x "$candidate" ] 2>/dev/null; then
        PHP_BIN="$candidate"
        break
    fi
done

if [ -z "$PHP_BIN" ]; then
    PHP_BIN="php"
fi

# Override php() function supaya semua `php` calls di script ini
# otomatis pakai binary PHP yang benar (aaPanel/custom build)
php() {
    command "$PHP_BIN" "$@"
}

# Force composer to run under the correct PHP binary.
# 'composer' command on aaPanel uses system PHP 8.3 by default,
# but we need PHP 8.4 for Laravel 13 / Symfony 8.x compatibility.
COMPOSER_BIN=$(command -v composer 2>/dev/null || command -v /usr/local/bin/composer 2>/dev/null || echo "")
composer() {
    if [ -n "$COMPOSER_BIN" ] && [ -x "$PHP_BIN" ]; then
        "$PHP_BIN" "$COMPOSER_BIN" "$@"
    else
        command composer "$@"
    fi
}

echo -e "${YELLOW}PHP binary: ${PHP_BIN} ($($PHP_BIN -r 'echo PHP_VERSION;' 2>/dev/null || echo 'unknown'))${NC}"
echo ""

# Project directory (edit this to match your VPS path)
PROJECT_DIR="${DEPLOY_PATH:-$(pwd)}"

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}  NotEDs Simulation - Deployment Script${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""

# Navigate to project directory
cd "$PROJECT_DIR" || error_exit "Tidak bisa masuk ke directory: $PROJECT_DIR"
echo -e "${YELLOW}Working directory: $(pwd)${NC}"
echo ""

# Detect web server user (www-data, www, apache, nginx, etc.)
WEB_USER="www-data"
for user in www www-data apache nginx http nobody; do
    if id "$user" &>/dev/null; then
        WEB_USER="$user"
        break
    fi
done
echo -e "${YELLOW}Web server user: ${WEB_USER}${NC}"

IS_ROOT=false
if [ "$EUID" -eq 0 ] 2>/dev/null || [ "$(id -u)" -eq 0 ] 2>/dev/null; then
    IS_ROOT=true
fi

if [ "$IS_ROOT" = false ]; then
    warn "Kamu tidak menjalankan script ini sebagai root/sudo."
    warn "Ownership changes (chown) ke '${WEB_USER}' mungkin gagal."
    warn "Jika encounter permission atau 500 errors, jalankan: sudo bash deploy.sh"
fi
echo ""

# ============================================================
# Pre-flight Checks — Validasi environment sebelum deploy
# ============================================================
info "Step 0: Pre-flight checks..."

# Check required PHP extensions
MISSING_EXTENSIONS=()
REQUIRED_EXTENSIONS=("pdo_mysql" "mbstring" "openssl" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo" "gd" "zip")

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m 2>/dev/null | grep -qi "^${ext}$"; then
        MISSING_EXTENSIONS+=("$ext")
    fi
done

if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then
    error_exit "PHP extension yang WAJIB tidak terinstall: ${MISSING_EXTENSIONS[*]}\n\nPHP binary: ${PHP_BIN}\n\nCara install:\n  Ubuntu/Debian: sudo apt install php$($PHP_BIN -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')-${MISSING_EXTENSIONS[*]// /-} \n  Atau: sudo apt install php-mysql php-mbstring php-xml php-gd php-zip php-bcmath\n  lalu restart web server."
fi

# Check optional but recommended extensions
OPTIONAL_MISSING=()
OPTIONAL_EXTENSIONS=("redis" "memcached" "imagick")

for ext in "${OPTIONAL_EXTENSIONS[@]}"; do
    if ! php -m 2>/dev/null | grep -qi "^${ext}$"; then
        OPTIONAL_MISSING+=("$ext")
    fi
done

if [ ${#OPTIONAL_MISSING[@]} -gt 0 ]; then
    warn "PHP extension optional tidak terinstall: ${OPTIONAL_MISSING[*]} (queue/cache mungkin terbatas)"
fi

# Check disk space (minimum 500MB free)
AVAILABLE_MB=$(df -m "$PROJECT_DIR" | awk 'NR==2 {print $4}')
if [ -n "$AVAILABLE_MB" ] && [ "$AVAILABLE_MB" -lt 500 ]; then
    warn "Disk space rendah: ${AVAILABLE_MB}MB tersisa (minimum 500MB disarankan)"
fi

# Check PHP version (minimum 8.2 for Laravel 11+)
PHP_VERSION=$(php -r 'echo PHP_VERSION;' 2>/dev/null || echo "0")
PHP_MAJOR=$(echo "$PHP_VERSION" | cut -d. -f1)
PHP_MINOR=$(echo "$PHP_VERSION" | cut -d. -f2)
if [ "$PHP_MAJOR" -lt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 2 ]); then
    error_exit "PHP version ${PHP_VERSION} terlalu rendah. Laravel 11+ membutuhkan PHP 8.2+."
fi

# Check if .env exists and has DB configured
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        warn ".env tidak ditemukan! Membuat dari .env.example — HARUS dikonfigurasi setelah deploy!"
    else
        error_exit ".env dan .env.example tidak ditemukan!"
    fi
fi

# Check database connectivity via PHP (non-interactive, no artisan needed)
if [ -f ".env" ]; then
    DB_DRIVER=$(grep -E '^DB_CONNECTION=' .env 2>/dev/null | cut -d= -f2 | tr -d '"' | tr -d "'")
    if [ "$DB_DRIVER" = "mysql" ] || [ "$DB_DRIVER" = "mariadb" ]; then
        info "  Mengecek koneksi database..."
        DB_TEST=$(php -r "
            \$env = parse_ini_file('.env');
            \$host = \$env['DB_HOST'] ?? '127.0.0.1';
            \$port = \$env['DB_PORT'] ?? '3306';
            \$db   = \$env['DB_DATABASE'] ?? '';
            \$user = \$env['DB_USERNAME'] ?? 'root';
            \$pass = \$env['DB_PASSWORD'] ?? '';
            try {
                \$pdo = new PDO('mysql:host='.\$host.';port='.\$port, \$user, \$pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                \$pdo->exec('CREATE DATABASE IF NOT EXISTS `'.\$db.'`');
                echo 'OK';
            } catch (Exception \$e) {
                echo 'FAIL:' . \$e->getMessage();
            }
        " 2>&1)

        if [[ "$DB_TEST" == FAIL:* ]]; then
            error_exit "Koneksi database GAGAL: ${DB_TEST#FAIL:}\n\nPastikan MySQL/MariaDB berjalan dan kredensial di .env benar."
        elif [ "$DB_TEST" != "OK" ]; then
            error_exit "Koneksi database GAGAL. Pastikan MySQL berjalan dan .env terkonfigurasi."
        fi
        success "  Database connection OK"
    fi
fi

success "Pre-flight checks passed."
echo ""

# Step 1: Create required directories & set permissions FIRST
info "Step 1: Preparing directories & permissions..."
mkdir -p storage/app/simulations
mkdir -p storage/app/private/simulations
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/testing
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache || true
chown -R "${WEB_USER}:${WEB_USER}" storage bootstrap/cache || true
success "Directories & permissions ready."
echo ""

# Step 2: Pull latest code
info "Step 2: Pulling latest code..."
DEPLOY_SCRIPT_HASH_BEFORE=$(md5sum "$0" 2>/dev/null | awk '{print $1}')
if [ -d ".git" ]; then
    # Stash local changes before pull (aman, bisa di-restore)
    git stash 2>/dev/null || true
    git pull origin main 2>/dev/null || git pull origin master 2>/dev/null || {
        warn "No git remote found, skipping pull."
    }
    success "Code updated."
else
    warn "Not a git repository, skipping pull."
fi

# Re-exec if deploy.sh was updated by git pull
DEPLOY_SCRIPT_HASH_AFTER=$(md5sum "$0" 2>/dev/null | awk '{print $1}')
if [ "$DEPLOY_SCRIPT_HASH_BEFORE" != "$DEPLOY_SCRIPT_HASH_AFTER" ]; then
    warn "deploy.sh updated by git pull, re-executing..."
    exec bash "$0" "$@"
fi
echo ""

# Step 3: Install PHP dependencies
# Use --no-scripts to avoid post-update-cmd errors (e.g. laravel/boost not installed in production)
info "Step 3: Installing composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts 2>&1 || {
    warn "Composer install had issues, trying update..."
    composer update --no-dev --optimize-autoloader --no-interaction --no-scripts 2>&1 || warn "Composer update juga bermasalah."
}
# Run post-install/update scripts manually (package:discover, vendor:publish, etc.)
# Skip boost:update as it's only available in dev environment
$PHP_BIN artisan package:discover --ansi 2>/dev/null || true
$PHP_BIN artisan vendor:publish --tag=laravel-assets --ansi --force 2>/dev/null || true
success "Composer done."
echo ""

# Step 4: Environment setup
info "Step 4: Checking .env file..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate --no-interaction
    warn ".env file created from .env.example. HARUS dikonfigurasi!"
else
    success ".env exists."
fi
echo ""

# Step 5: Clear old cache FIRST (prevents stale cache errors)
info "Step 5: Clearing old cache..."
php artisan config:clear --no-interaction 2>/dev/null || true
php artisan route:clear --no-interaction 2>/dev/null || true
php artisan view:clear --no-interaction 2>/dev/null || true
php artisan event:clear --no-interaction 2>/dev/null || true
success "Cache cleared."
echo ""

# Step 6: Run migrations (CRITICAL — gagal = stop)
info "Step 6: Running migrations..."
MIGRATION_OUTPUT=$(php artisan migrate --force --no-interaction 2>&1)
MIGRATION_EXIT=$?

if [ $MIGRATION_EXIT -ne 0 ]; then
    echo -e "${RED}Migration output:${NC}"
    echo "$MIGRATION_OUTPUT"
    echo ""
    error_exit "Migration GAGAL! Aplikasi tidak bisa berjalan tanpa database schema yang benar.\n\nKemungkinan penyebab:\n  1. MySQL/MariaDB tidak berjalan\n  2. PHP extension pdo_mysql tidak terinstall\n  3. Kredensial database di .env salah\n  4. User database tidak punya hak akses"
fi

# Check if there are pending migrations
if echo "$MIGRATION_OUTPUT" | grep -q "Nothing to migrate"; then
    success "Migrations: Tidak ada yang perlu di-migrate (sudah up-to-date)."
else
    success "Migrations berhasil dijalankan."
fi
echo ""

# Step 7: Seed superadmin (tidak critical — superadmin mungkin sudah ada)
info "Step 7: Running superadmin seeder..."
SEEDER_OUTPUT=$(php artisan db:seed --class=SuperAdminSeeder --force --no-interaction 2>&1)
SEEDER_EXIT=$?

if [ $SEEDER_EXIT -ne 0 ]; then
    # Cek apakah error karena "already exists" (ini normal)
    if echo "$SEEDER_OUTPUT" | grep -qi "already exists\|unique\|duplicate"; then
        success "SuperAdmin sudah ada, seeder skipped."
    else
        warn "Seeder error (non-critical): $(echo "$SEEDER_OUTPUT" | tail -1)"
    fi
else
    success "SuperAdmin seeder berhasil."
fi
echo ""

# Step 8: Storage link
info "Step 8: Creating storage link..."
php artisan storage:link --force 2>/dev/null || true
success "Storage link ready."
echo ""

# Step 9: Install npm dependencies & build
info "Step 9: Building frontend assets..."
if [ -f "package.json" ]; then
    npm ci --legacy-peer-deps 2>/dev/null || npm install --legacy-peer-deps 2>/dev/null || warn "npm install bermasalah."
    npm run build 2>&1 || warn "Frontend build bermasalah."
    success "Frontend built."
else
    warn "No package.json found, skipping frontend build."
fi
echo ""

# Step 10: Fix ownership BEFORE cache (so cache files are owned by web user)
info "Step 10: Setting ownership before cache..."
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p bootstrap/cache
chown -R "${WEB_USER}:${WEB_USER}" storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true
# Ensure public directory is writable for sitemap.xml and build artifacts
chown -R "${WEB_USER}:${WEB_USER}" public/ || true
success "Ownership set."
echo ""

# Step 11: Rebuild cache (as root, but chown after)
info "Step 11: Optimizing application..."
php artisan config:cache --no-interaction 2>/dev/null || true
php artisan route:cache --no-interaction 2>/dev/null || true
php artisan view:cache --no-interaction 2>/dev/null || true
php artisan event:cache --no-interaction 2>/dev/null || true
success "Cache optimized."
echo ""

# Step 12: Generate sitemap.xml (after cache is warm, before ownership fix)
info "Step 12: Generating sitemap..."
php artisan sitemap:generate --no-interaction 2>/dev/null || warn "Sitemap generation failed (non-critical)."
# Ensure sitemap.xml is writable by web server on future observer-triggered regenerations
if [ -f "public/sitemap.xml" ]; then
    chown "${WEB_USER}:${WEB_USER}" public/sitemap.xml 2>/dev/null || true
    chmod 664 public/sitemap.xml 2>/dev/null || true
fi
success "Sitemap generated."
echo ""

# Step 13: Fix ownership AFTER cache (files created by root need web user ownership)
info "Step 13: Fixing ownership after cache..."
chown -R "${WEB_USER}:${WEB_USER}" storage/framework/views || true
chown -R "${WEB_USER}:${WEB_USER}" storage/framework/cache || true
chown -R "${WEB_USER}:${WEB_USER}" storage/framework/sessions || true
chown -R "${WEB_USER}:${WEB_USER}" storage/logs || true
chown -R "${WEB_USER}:${WEB_USER}" bootstrap/cache || true
chown -R "${WEB_USER}:${WEB_USER}" public/ || true
chmod -R 775 storage bootstrap/cache || true
success "Ownership fixed."
echo ""

# Step 14: Restart queue worker
info "Step 14: Restarting queue worker..."
QUEUE_OUTPUT=$(php artisan queue:restart 2>&1)
if echo "$QUEUE_OUTPUT" | grep -qi "Redis\|Class.*not found"; then
    warn "Queue restart: Redis extension tidak tersedia. Queue berjalan dengan database driver."
else
    success "Queue restarted."
fi
echo ""

# ============================================================
# Deployment Summary
# ============================================================
echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}  Deployment selesai!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""

# Show warnings summary
if [ $DEPLOY_WARNINGS -gt 0 ]; then
    echo -e "${YELLOW}⚠  ${DEPLOY_WARNINGS} warning(s) — lihat output di atas.${NC}"
    echo ""
fi

echo -e "${YELLOW}Superadmin credentials:${NC}"
echo -e "   Email:    info@noteds.com"
echo -e "   Password: (lihat di .env atau database — TIDAK ditampilkan demi keamanan)"
echo ""
echo -e "${BLUE}Access:${NC}"
APP_URL=$(php artisan tinker --execute 'echo config("app.url");' 2>/dev/null || echo 'http://noteds.com')
echo -e "   Landing:  ${APP_URL}"
echo -e "   Admin:    ${APP_URL}/admin/dashboard"
echo ""

# Final health check
info "Running final health check..."
HEALTH_OK=true
php artisan about 2>/dev/null | grep -q "Environment" || { warn "Health check: artisan about tidak bisa dijalankan"; HEALTH_OK=false; }
if [ "$HEALTH_OK" = true ]; then
    success "Health check passed."
fi
echo ""
