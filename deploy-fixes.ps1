# Deploy bug fixes to production
# SAFE: Only edits existing files, no deletions

$server = "root@103.185.52.124"
$remotePath = "/var/www/kitorangpeduli.id"

Write-Host "Deploying bug fixes to production..." -ForegroundColor Green
Write-Host "SAFE MODE: Only editing existing files, no deletions" -ForegroundColor Yellow
Write-Host ""

# Files to deploy
$files = @(
    "app/Http/Controllers/HomeController.php",
    "app/Http/Controllers/QuestionnaireController.php",
    "resources/views/partials/questionnaire-cards.blade.php",
    "resources/views/questionnaire/fill.blade.php"
)

Write-Host "Files to deploy:" -ForegroundColor Cyan
foreach ($file in $files) {
    Write-Host "   - $file" -ForegroundColor Gray
}
Write-Host ""

# Backup first
Write-Host "Creating backup on server..." -ForegroundColor Yellow
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
ssh $server "mkdir -p /var/www/backups/kitorangpeduli_$timestamp"

foreach ($file in $files) {
    $backupPath = "/var/www/backups/kitorangpeduli_$timestamp/$file"
    $backupDir = Split-Path $backupPath -Parent
    ssh $server "mkdir -p $backupDir && cp $remotePath/$file $backupPath 2>/dev/null || true"
}

Write-Host "Backup created at: /var/www/backups/kitorangpeduli_$timestamp" -ForegroundColor Green
Write-Host ""

# Deploy files
Write-Host "Uploading files..." -ForegroundColor Cyan
foreach ($file in $files) {
    Write-Host "   Uploading: $file" -ForegroundColor Gray

    # Create directory if needed
    $remoteDir = Split-Path "$remotePath/$file" -Parent
    ssh $server "mkdir -p $remoteDir"

    # Copy file
    scp "$file" "${server}:$remotePath/$file"

    if ($LASTEXITCODE -eq 0) {
        Write-Host "   Success" -ForegroundColor Green
    } else {
        Write-Host "   Failed" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Optimizing application..." -ForegroundColor Yellow

# Clear caches and optimize
ssh $server @"
cd $remotePath
/usr/bin/php8.2 artisan config:clear
/usr/bin/php8.2 artisan route:clear
/usr/bin/php8.2 artisan view:clear
/usr/bin/php8.2 artisan cache:clear
/usr/bin/php8.2 artisan config:cache
/usr/bin/php8.2 artisan route:cache
/usr/bin/php8.2 artisan view:cache
"@

Write-Host ""
Write-Host "Deployment completed successfully!" -ForegroundColor Green
Write-Host "Application: https://kitorangpeduli.id" -ForegroundColor Cyan
Write-Host "Backup location: /var/www/backups/kitorangpeduli_$timestamp" -ForegroundColor Gray
Write-Host ""
Write-Host "Changes deployed:" -ForegroundColor Yellow
Write-Host "   1. Fixed questionnaire cards boolean logic error" -ForegroundColor Gray
Write-Host "   2. Added existing answers restore for draft questionnaires" -ForegroundColor Gray
Write-Host ""
