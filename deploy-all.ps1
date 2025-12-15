# Full deployment to production
# SAFE: Only edits existing files and adds new files, no deletions

$server = "root@103.185.52.124"
$remotePath = "/var/www/kitorangpeduli.id"

Write-Host "Starting full deployment to production..." -ForegroundColor Green
Write-Host ""

# Controllers
$controllers = @(
    "app/Http/Controllers/HomeController.php",
    "app/Http/Controllers/QuestionnaireController.php",
    "app/Http/Controllers/AuthController.php"
)

# Views
$views = @(
    "resources/views/partials/questionnaire-cards.blade.php",
    "resources/views/questionnaire/fill.blade.php",
    "resources/views/auth/register.blade.php",
    "resources/views/home.blade.php",
    "resources/views/layouts/app.blade.php",
    "resources/views/questionnaire/success.blade.php"
)

# Routes
$routes = @(
    "routes/web.php"
)

$allFiles = $controllers + $views + $routes

Write-Host "Files to deploy: $($allFiles.Count)" -ForegroundColor Cyan
Write-Host ""

# Create backup
Write-Host "Creating backup..." -ForegroundColor Yellow
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
ssh $server "mkdir -p /var/www/backups/full_deploy_$timestamp"

# Deploy each file
$successCount = 0
$failCount = 0

foreach ($file in $allFiles) {
    if (Test-Path $file) {
        Write-Host "Deploying: $file" -ForegroundColor Gray

        # Backup if exists
        $backupPath = "/var/www/backups/full_deploy_$timestamp/$file"
        $backupDir = Split-Path $backupPath -Parent
        ssh $server "mkdir -p $backupDir && cp $remotePath/$file $backupPath 2>/dev/null || true"

        # Create remote directory
        $remoteDir = Split-Path "$remotePath/$file" -Parent
        ssh $server "mkdir -p $remoteDir"

        # Copy file
        scp "$file" "${server}:$remotePath/$file" 2>$null

        if ($LASTEXITCODE -eq 0) {
            $successCount++
            Write-Host "  [OK]" -ForegroundColor Green
        } else {
            $failCount++
            Write-Host "  [FAILED]" -ForegroundColor Red
        }
    } else {
        Write-Host "Skipping (not found): $file" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "Upload summary: $successCount success, $failCount failed" -ForegroundColor Cyan
Write-Host ""

# Clear all caches
Write-Host "Clearing caches..." -ForegroundColor Yellow
ssh $server "cd $remotePath && /usr/bin/php8.2 artisan config:clear"
ssh $server "cd $remotePath && /usr/bin/php8.2 artisan route:clear"
ssh $server "cd $remotePath && /usr/bin/php8.2 artisan view:clear"
ssh $server "cd $remotePath && /usr/bin/php8.2 artisan cache:clear"

Write-Host ""
Write-Host "Re-caching..." -ForegroundColor Yellow
ssh $server "cd $remotePath && /usr/bin/php8.2 artisan config:cache"
ssh $server "cd $remotePath && /usr/bin/php8.2 artisan route:cache"
ssh $server "cd $remotePath && /usr/bin/php8.2 artisan view:cache"

Write-Host ""
Write-Host "Deployment completed!" -ForegroundColor Green
Write-Host "Application: https://kitorangpeduli.id" -ForegroundColor Cyan
Write-Host "Backup: /var/www/backups/full_deploy_$timestamp" -ForegroundColor Gray
Write-Host ""
