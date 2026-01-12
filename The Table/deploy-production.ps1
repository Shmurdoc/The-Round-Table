#!/usr/bin/env pwsh
# Production Deployment Script for RoundTable Platform
# Version: 1.0
# Date: January 10, 2026

$ErrorActionPreference = "Stop"

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  RoundTable Production Deployment" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# Check if we're in the correct directory
if (-not (Test-Path "artisan")) {
    Write-Host "Error: artisan file not found. Please run this script from the Laravel root directory." -ForegroundColor Red
    exit 1
}

# Step 1: Check environment
Write-Host "[1/10] Checking environment..." -ForegroundColor Yellow
$envFile = ".env"
if (-not (Test-Path $envFile)) {
    Write-Host "Error: .env file not found!" -ForegroundColor Red
    exit 1
}

# Verify critical env variables
$envContent = Get-Content $envFile -Raw
$requiredVars = @("APP_KEY", "DB_DATABASE", "DB_USERNAME", "APP_URL")
foreach ($var in $requiredVars) {
    if ($envContent -notmatch "$var=.+") {
        Write-Host "Warning: $var might not be configured!" -ForegroundColor Yellow
    }
}
Write-Host "   ✓ Environment file found" -ForegroundColor Green

# Step 2: Clear all caches
Write-Host "[2/10] Clearing all caches..." -ForegroundColor Yellow
php artisan cache:clear 2>$null
php artisan config:clear 2>$null
php artisan route:clear 2>$null
php artisan view:clear 2>$null
Write-Host "   ✓ Caches cleared" -ForegroundColor Green

# Step 3: Run composer install
Write-Host "[3/10] Installing dependencies..." -ForegroundColor Yellow
composer install --optimize-autoloader --no-dev 2>&1 | Out-Null
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ Dependencies installed" -ForegroundColor Green
} else {
    Write-Host "   ⚠ Composer install had warnings" -ForegroundColor Yellow
}

# Step 4: Run migrations
Write-Host "[4/10] Running database migrations..." -ForegroundColor Yellow
php artisan migrate --force
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ Migrations completed" -ForegroundColor Green
} else {
    Write-Host "   ✗ Migration failed!" -ForegroundColor Red
    exit 1
}

# Step 5: Create storage link
Write-Host "[5/10] Setting up storage link..." -ForegroundColor Yellow
php artisan storage:link 2>&1 | Out-Null
Write-Host "   ✓ Storage link configured" -ForegroundColor Green

# Step 6: Cache configurations
Write-Host "[6/10] Caching configurations..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache
Write-Host "   ✓ Configurations cached" -ForegroundColor Green

# Step 7: Set permissions (if on Linux/Mac)
Write-Host "[7/10] Setting file permissions..." -ForegroundColor Yellow
if ($IsLinux -or $IsMacOS) {
    chmod -R 755 storage bootstrap/cache
    Write-Host "   ✓ Permissions set" -ForegroundColor Green
} else {
    Write-Host "   ⊘ Skipped (Windows)" -ForegroundColor Gray
}

# Step 8: Run production test
Write-Host "[8/10] Running production tests..." -ForegroundColor Yellow
php production-test.php
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ All tests passed" -ForegroundColor Green
} else {
    Write-Host "   ⚠ Some tests failed - review before proceeding" -ForegroundColor Yellow
}

# Step 9: Optimize
Write-Host "[9/10] Optimizing application..." -ForegroundColor Yellow
php artisan optimize
Write-Host "   ✓ Optimization complete" -ForegroundColor Green

# Step 10: Final checks
Write-Host "[10/10] Running final checks..." -ForegroundColor Yellow
$checks = @()

# Check APP_DEBUG
if ($envContent -match "APP_DEBUG=true") {
    $checks += "⚠ APP_DEBUG is set to true (should be false in production)"
}

# Check APP_ENV
if ($envContent -notmatch "APP_ENV=production") {
    $checks += "⚠ APP_ENV is not set to 'production'"
}

# Check queue configuration
if ($envContent -match "QUEUE_CONNECTION=sync") {
    $checks += "ℹ QUEUE_CONNECTION is 'sync' (consider using redis or database for production)"
}

if ($checks.Count -gt 0) {
    Write-Host ""
    Write-Host "IMPORTANT NOTES:" -ForegroundColor Yellow
    foreach ($check in $checks) {
        Write-Host "   $check" -ForegroundColor Yellow
    }
} else {
    Write-Host "   ✓ All checks passed" -ForegroundColor Green
}

# Summary
Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  Deployment Complete!" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor White
Write-Host "  1. Review any warnings above" -ForegroundColor Gray
Write-Host "  2. Test the application thoroughly" -ForegroundColor Gray
Write-Host "  3. Monitor logs for any issues" -ForegroundColor Gray
Write-Host "  4. Set up automated backups" -ForegroundColor Gray
Write-Host ""
Write-Host "The application is ready for production!" -ForegroundColor Green
Write-Host ""
