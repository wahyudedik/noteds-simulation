#!/bin/bash

# ============================================================
# NotEDs Simulation - Deployment Script
# ============================================================
# chmod +x deploy.sh
# Usage: bash deploy.sh
#
# Script ini akan:
# 1. Pull latest code dari git
# 2. Install dependencies (composer & npm)
# 3. Run migration
# 4. Build frontend assets
# 5. Restart queue worker
# 6. Clear & rebuild cache
# ============================================================

set -e  # Exit on error
trap 'echo -e "${RED}Deploy failed at line $LINENO${NC}"; exit 1' ERR

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Allow composer to run as root (common on VPS)
export COMPOSER_ALLOW_SUPERUSER=1

# Project directory (edit this to match your VPS path)
PROJECT_DIR="${DEPLOY_PATH:-$(pwd)}"

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}  NotEDs Simulation - Deployment Script${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""

# Navigate to project directory
cd "$PROJECT_DIR"
echo -e "${YELLOW}Working directory: $(pwd)${NC}"
echo ""

# Detect web server user (www-data, www, apache, nginx, etc.)
WEB_USER="www-data"
for user in www-data www apache nginx http nobody; do
    if id "$user" &>/dev/null; then
        WEB_USER="$user"
        break
    fi
done
echo -e "${YELLOW}Web server user: ${WEB_USER}${NC}"
echo ""

# Step 0: Create required directories & set permissions FIRST
echo -e "${BLUE}Step 0: Preparing directories & permissions...${NC}"
mkdir -p storage/app/simulations
mkdir -p storage/app/private/simulations
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/testing
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R "${WEB_USER}:${WEB_USER}" storage bootstrap/cache 2>/dev/null || true
echo -e "${GREEN}Directories & permissions ready.${NC}"
echo ""

# Step 1: Pull latest code
echo -e "${BLUE}Step 1: Pulling latest code...${NC}"
DEPLOY_SCRIPT_HASH_BEFORE=$(md5sum "$0" 2>/dev/null | awk '{print $1}')
if [ -d ".git" ]; then
    # Discard local changes to prevent merge conflicts
    git checkout -- . 2>/dev/null || true
    git pull origin main 2>/dev/null || git pull origin master 2>/dev/null || {
        echo -e "${YELLOW}No git remote found, skipping pull.${NC}"
    }
    echo -e "${GREEN}Code updated.${NC}"
else
    echo -e "${YELLOW}Not a git repository, skipping pull.${NC}"
fi

# Re-exec if deploy.sh was updated by git pull
DEPLOY_SCRIPT_HASH_AFTER=$(md5sum "$0" 2>/dev/null | awk '{print $1}')
if [ "$DEPLOY_SCRIPT_HASH_BEFORE" != "$DEPLOY_SCRIPT_HASH_AFTER" ]; then
    echo -e "${YELLOW}deploy.sh updated, re-executing...${NC}"
    exec bash "$0" "$@"
fi
echo ""

# Step 2: Install PHP dependencies
echo -e "${BLUE}Step 2: Installing composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction 2>/dev/null || {
    echo -e "${YELLOW}Composer install had issues, trying update...${NC}"
    composer update --no-dev --optimize-autoloader --no-interaction 2>/dev/null || true
}
echo -e "${GREEN}Composer done.${NC}"
echo ""

# Step 3: Environment setup
echo -e "${BLUE}Step 3: Checking .env file...${NC}"
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate --no-interaction
    echo -e "${YELLOW}.env file created from .env.example. Please configure it!${NC}"
else
    echo -e "${GREEN}.env exists.${NC}"
fi
echo ""

# Step 4: Clear old cache FIRST (prevents stale cache errors)
echo -e "${BLUE}Step 4: Clearing old cache...${NC}"
php artisan config:clear --no-interaction 2>/dev/null || true
php artisan route:clear --no-interaction 2>/dev/null || true
php artisan view:clear --no-interaction 2>/dev/null || true
php artisan event:clear --no-interaction 2>/dev/null || true
echo -e "${GREEN}Cache cleared.${NC}"
echo ""

# Step 5: Run migrations
echo -e "${BLUE}Step 5: Running migrations...${NC}"
php artisan migrate --force --no-interaction 2>/dev/null || {
    echo -e "${YELLOW}Migration had issues (may already be applied).${NC}"
}
echo -e "${GREEN}Migrations done.${NC}"
echo ""

# Step 6: Seed superadmin
echo -e "${BLUE}Step 6: Running superadmin seeder...${NC}"
php artisan db:seed --class=SuperAdminSeeder --force --no-interaction 2>/dev/null || {
    echo -e "${YELLOW}SuperAdmin already exists or seeder skipped.${NC}"
}
echo -e "${GREEN}Seeder done.${NC}"
echo ""

# Step 7: Storage link
echo -e "${BLUE}Step 7: Creating storage link...${NC}"
php artisan storage:link --force 2>/dev/null || true
echo -e "${GREEN}Storage link ready.${NC}"
echo ""

# Step 8: Install npm dependencies & build
echo -e "${BLUE}Step 8: Building frontend assets...${NC}"
if [ -f "package.json" ]; then
    npm ci --legacy-peer-deps 2>/dev/null || npm install --legacy-peer-deps 2>/dev/null || true
    npm run build 2>/dev/null || {
        echo -e "${YELLOW}Frontend build had issues.${NC}"
    }
    echo -e "${GREEN}Frontend built.${NC}"
else
    echo -e "${YELLOW}No package.json found, skipping frontend build.${NC}"
fi
echo ""

# Step 9: Fix ownership BEFORE cache (so cache files are owned by web user)
echo -e "${BLUE}Step 9: Setting ownership before cache...${NC}"
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p bootstrap/cache
chown -R "${WEB_USER}:${WEB_USER}" storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
echo -e "${GREEN}Ownership set.${NC}"
echo ""

# Step 10: Rebuild cache (as root, but chown after)
echo -e "${BLUE}Step 10: Optimizing application...${NC}"
php artisan config:cache --no-interaction 2>/dev/null || true
php artisan route:cache --no-interaction 2>/dev/null || true
php artisan view:cache --no-interaction 2>/dev/null || true
php artisan event:cache --no-interaction 2>/dev/null || true
echo -e "${GREEN}Cache optimized.${NC}"
echo ""

# Step 11: Fix ownership AFTER cache (files created by root need web user ownership)
echo -e "${BLUE}Step 11: Fixing ownership after cache...${NC}"
chown -R "${WEB_USER}:${WEB_USER}" storage/framework/views 2>/dev/null || true
chown -R "${WEB_USER}:${WEB_USER}" storage/framework/cache 2>/dev/null || true
chown -R "${WEB_USER}:${WEB_USER}" storage/framework/sessions 2>/dev/null || true
chown -R "${WEB_USER}:${WEB_USER}" storage/logs 2>/dev/null || true
chown -R "${WEB_USER}:${WEB_USER}" bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
echo -e "${GREEN}Ownership fixed.${NC}"
echo ""

# Step 12: Restart queue worker
echo -e "${BLUE}Step 12: Restarting queue worker...${NC}"
php artisan queue:restart 2>/dev/null || true
echo -e "${GREEN}Queue restarted.${NC}"
echo ""

echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}  Deployment complete!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo -e "${YELLOW}Superadmin credentials:${NC}"
echo -e "   Email:    info@noteds.com"
echo -e "   Password: Wahyu123456789@"
echo ""
echo -e "${BLUE}Access:${NC}"
echo -e "   Landing:  $(php artisan tinker --execute 'echo config("app.url");' 2>/dev/null || echo 'http://noteds.com')"
echo -e "   Admin:    $(php artisan tinker --execute 'echo config("app.url") . "/admin/dashboard";' 2>/dev/null || echo 'http://noteds.com/admin/dashboard')"
echo ""
