@echo off
REM Run this script AFTER you've dumped the production database
REM This will download and restore the data to local database

echo ============================================
echo KitorangPeduli Production Data Restore
echo ============================================
echo.

REM Step 1: Download dump from production server
echo [Step 1/3] Downloading data from production...
scp root@103.185.52.124:/tmp/kitorangpeduli_backup.sql ./kitorangpeduli_backup.sql

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to download dump file
    echo.
    echo Please run this command on production server first:
    echo   ssh root@103.185.52.124
    echo   cd /var/www/kitorangpeduli.id
    echo   sudo -u postgres pg_dump -d kitorangpeduli_db --data-only \
    echo     --exclude-table=provinces --exclude-table=regencies \
    echo     --exclude-table=districts --exclude-table=villages \
    echo     ^> /tmp/kitorangpeduli_backup.sql
    pause
    exit /b 1
)

echo SUCCESS: Downloaded dump file
echo.

REM Step 2: Restore to local database
echo [Step 2/3] Restoring data to local database...
psql -U postgres -d kitorangpeduli_db -f kitorangpeduli_backup.sql

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to restore data
    pause
    exit /b 1
)

echo SUCCESS: Data restored
echo.

REM Step 3: Verify
echo [Step 3/3] Verifying data...
php check_data.php

echo.
echo ============================================
echo Restore completed successfully!
echo ============================================
pause
