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

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Project directory (edit this to match your VPS path)
PROJECT_DIR="${DEPLOY_PATH:-$(pwd)}"

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}  🚀 NotEDs Simulation - Deployment Script     ${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""

# Navigate to project directory
cd "$PROJECT_DIR"
echo -e "${YELLOW}📁 Working directory: $(pwd)${NC}"
echo ""

# Step 1: Pull latest code
echo -e "${BLUE}Step 1: Pulling latest code...${NC}"
if [ -d ".git" ]; then
    git pull origin main 2>/dev/null || git pull origin master 2>/dev/null || {
        echo -e "${YELLOW}⚠️  No git remote found, skipping pull.${NC}"
    }
    echo -e "${GREEN}✅ Code updated.${NC}"
else
    echo -e "${YELLOW}⚠️  Not a git repository, skipping pull.${NC}"
fi
echo ""

# Step 2: Install PHP dependencies
echo -e "${BLUE}Step 2: Installing composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
echo -e "${GREEN}✅ Composer done.${NC}"
echo ""

# Step 3: Environment setup
echo -e "${BLUE}Step 3: Checking .env file...${NC}"
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate --no-interaction
    echo -e "${YELLOW}⚠️  .env file created from .env.example. Please configure it!${NC}"
else
    echo -e "${GREEN}✅ .env exists.${NC}"
fi
echo ""

# Step 4: Run migrations
echo -e "${BLUE}Step 4: Running migrations...${NC}"
php artisan migrate --force --no-interaction
echo -e "${GREEN}✅ Migrations done.${NC}"
echo ""

# Step 5: Seed superadmin
echo -e "${BLUE}Step 5: Running superadmin seeder...${NC}"
php artisan db:seed --class=SuperAdminSeeder --force --no-interaction 2>/dev/null || {
    echo -e "${YELLOW}⚠️  SuperAdmin already exists or seeder skipped.${NC}"
}
echo -e "${GREEN}✅ Seeder done.${NC}"
echo ""

# Step 6: Storage link
echo -e "${BLUE}Step 6: Creating storage link...${NC}"
php artisan storage:link --force 2>/dev/null || true
echo -e "${GREEN}✅ Storage link ready.${NC}"
echo ""

# Step 7: Install npm dependencies & build
echo -e "${BLUE}Step 7: Building frontend assets...${NC}"
if [ -f "package.json" ]; then
    npm ci --legacy-peer-deps 2>/dev/null || npm install --legacy-peer-deps
    npm run build
    echo -e "${GREEN}✅ Frontend built.${NC}"
else
    echo -e "${YELLOW}⚠️  No package.json found, skipping frontend build.${NC}"
fi
echo ""

# Step 8: Clear and rebuild cache
echo -e "${BLUE}Step 8: Optimizing application...${NC}"
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction
php artisan event:cache --no-interaction
echo -e "${GREEN}✅ Cache optimized.${NC}"
echo ""

# Step 9: Restart queue worker
echo -e "${BLUE}Step 9: Restarting queue worker...${NC}"
php artisan queue:restart 2>/dev/null || true
echo -e "${GREEN}✅ Queue restarted.${NC}"
echo ""

# Step 10: Set permissions
echo -e "${BLUE}Step 10: Setting permissions...${NC}"
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
echo -e "${GREEN}✅ Permissions set.${NC}"
echo ""

echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}  ✅ Deployment complete!                        ${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo -e "${YELLOW}📌 Superadmin credentials:${NC}"
echo -e "   Email:    info@noteds.com"
echo -e "   Password: Wahyu123456789@"
echo ""
echo -e "${BLUE}🔗 Access:${NC}"
echo -e "   Landing:  $(php artisan tinker --execute 'echo config("app.url");' 2>/dev/null || echo 'http://your-domain.test')"
echo -e "   Admin:    $(php artisan tinker --execute 'echo config("app.url") . "/admin/dashboard";' 2>/dev/null || echo 'http://your-domain.test/admin/dashboard')"
echo ""
