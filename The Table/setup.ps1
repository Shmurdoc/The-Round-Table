# RoundTable Quick Setup Script
# Run this in PowerShell from the project directory

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "RoundTable System Quick Setup" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

$projectPath = "C:\wamp64\www\The round table\The Table"

# Check if we're in the right directory
if (-not (Test-Path "$projectPath\artisan")) {
    Write-Host "ERROR: Not in project directory. Please cd to: $projectPath" -ForegroundColor Red
    exit
}

Write-Host "[1/8] Checking PHP and Composer..." -ForegroundColor Yellow
php --version
composer --version

Write-Host "`n[2/8] Installing Composer dependencies..." -ForegroundColor Yellow
composer install --optimize-autoloader

Write-Host "`n[3/8] Generating application key..." -ForegroundColor Yellow
php artisan key:generate

Write-Host "`n[4/8] Creating database..." -ForegroundColor Yellow
Write-Host "Please ensure MySQL is running and create database 'roundtable'" -ForegroundColor Cyan
Write-Host "Run in MySQL: CREATE DATABASE roundtable CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" -ForegroundColor Cyan
$confirm = Read-Host "Press Enter when database is created..."

Write-Host "`n[5/8] Running migrations..." -ForegroundColor Yellow
php artisan migrate

Write-Host "`n[6/8] Creating storage symlink..." -ForegroundColor Yellow
php artisan storage:link

Write-Host "`n[7/8] Copying Argon Dashboard assets..." -ForegroundColor Yellow
$argonPath = "..\argon-dashboard-master\assets"
if (Test-Path $argonPath) {
    Copy-Item "$argonPath\css" -Destination "public\" -Recurse -Force
    Copy-Item "$argonPath\js" -Destination "public\" -Recurse -Force
    Copy-Item "$argonPath\img" -Destination "public\" -Recurse -Force
    Copy-Item "$argonPath\fonts" -Destination "public\" -Recurse -Force
    Write-Host "Argon Dashboard assets copied successfully!" -ForegroundColor Green
} else {
    Write-Host "WARNING: Argon Dashboard not found at $argonPath" -ForegroundColor Red
    Write-Host "Please manually copy assets from argon-dashboard-master/assets/ to public/" -ForegroundColor Yellow
}

Write-Host "`n[8/8] Starting development server..." -ForegroundColor Yellow
Write-Host ""
Write-Host "==================================" -ForegroundColor Green
Write-Host "Setup Complete!" -ForegroundColor Green
Write-Host "==================================" -ForegroundColor Green
Write-Host ""
Write-Host "Access your application at: http://localhost:8000" -ForegroundColor Cyan
Write-Host ""
Write-Host "Default credentials will be created on first run." -ForegroundColor Yellow
Write-Host ""
Write-Host "Starting server..." -ForegroundColor Yellow
php artisan serve
