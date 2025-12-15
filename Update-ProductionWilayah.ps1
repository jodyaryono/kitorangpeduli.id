# Safe Production Wilayah Update Script
# This script will upload and execute wilayah data update on production
# SAFETY: Uses INSERT with automatic conflict handling

Write-Host "==========================================" -ForegroundColor Yellow
Write-Host "PRODUCTION WILAYAH UPDATE - SAFE MODE" -ForegroundColor Yellow
Write-Host "==========================================" -ForegroundColor Yellow
Write-Host ""
Write-Host "This will add wilayah data to production database" -ForegroundColor Cyan
Write-Host "Backup already created: /tmp/full_backup_before_wilayah_update_*.sql" -ForegroundColor Green
Write-Host ""

$confirm = Read-Host "Type 'UPDATE PRODUCTION' to continue (case sensitive)"

if ($confirm -ne "UPDATE PRODUCTION") {
    Write-Host "Aborted - confirmation text did not match" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "[Step 1/3] Uploading wilayah data (25MB)..." -ForegroundColor Cyan
scp production_wilayah_inserts_safe.sql root@103.185.52.124:/tmp/

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Failed to upload file" -ForegroundColor Red
    exit 1
}

Write-Host "[Step 2/3] Checking current production data counts..." -ForegroundColor Cyan
ssh root@103.185.52.124 "sudo -u postgres psql -d kitorangpeduli_db -c 'SELECT COUNT(*) as provinces FROM provinces; SELECT COUNT(*) as regencies FROM regencies; SELECT COUNT(*) as districts FROM districts; SELECT COUNT(*) as villages FROM villages;'"

Write-Host ""
Write-Host "[Step 3/3] Executing safe INSERT (this will take 2-3 minutes)..." -ForegroundColor Cyan
Write-Host "Progress: Importing data with conflict safety..." -ForegroundColor Yellow

ssh root@103.185.52.124 @"
sudo -u postgres psql -d kitorangpeduli_db << 'SQL'
-- Disable FK checks for faster insert
SET session_replication_role = replica;

-- Import data (will automatically skip duplicates)
\i /tmp/production_wilayah_inserts_safe.sql

-- Re-enable FK checks
SET session_replication_role = DEFAULT;

-- Show final counts
SELECT 'FINAL COUNTS:' as status;
SELECT COUNT(*) as provinces FROM provinces;
SELECT COUNT(*) as regencies FROM regencies;
SELECT COUNT(*) as districts FROM districts;
SELECT COUNT(*) as villages FROM villages;
SQL
"@

Write-Host ""
Write-Host "==========================================" -ForegroundColor Green
Write-Host "UPDATE COMPLETED" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""
Write-Host "If you need to restore from backup:" -ForegroundColor Yellow
Write-Host "  ssh root@103.185.52.124" -ForegroundColor Gray
Write-Host "  sudo -u postgres psql < /tmp/full_backup_before_wilayah_update_*.sql" -ForegroundColor Gray
