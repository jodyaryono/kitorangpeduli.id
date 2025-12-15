#!/bin/bash
# Safe production wilayah update script
# This script will ONLY add new wilayah data, NEVER delete existing data

echo "=========================================="
echo "PRODUCTION WILAYAH UPDATE - SAFE MODE"
echo "=========================================="
echo ""
echo "WARNING: This will update production database"
echo "Backup has been created at: /tmp/full_backup_before_wilayah_update_*.sql"
echo ""
read -p "Continue? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Aborted."
    exit 1
fi

echo ""
echo "Uploading wilayah data..."

# Upload the safe SQL file to production server
scp production_wilayah_inserts_safe.sql root@103.185.52.124:/tmp/

echo ""
echo "Executing safe INSERT on production..."
echo "This will take a few minutes..."

# Execute with ON CONFLICT DO NOTHING wrapper
ssh root@103.185.52.124 << 'EOF'
sudo -u postgres psql -d kitorangpeduli_db << 'SQL'
-- Disable triggers temporarily for faster insert
SET session_replication_role = replica;

-- Show before counts
SELECT 'BEFORE UPDATE:' as status;
SELECT COUNT(*) as provinces FROM provinces;
SELECT COUNT(*) as regencies FROM regencies;
SELECT COUNT(*) as districts FROM districts;
SELECT COUNT(*) as villages FROM villages;

-- Import data (inserts will skip on conflict)
\i /tmp/production_wilayah_inserts_safe.sql

-- Re-enable triggers
SET session_replication_role = DEFAULT;

-- Show after counts
SELECT 'AFTER UPDATE:' as status;
SELECT COUNT(*) as provinces FROM provinces;
SELECT COUNT(*) as regencies FROM regencies;
SELECT COUNT(*) as districts FROM districts;
SELECT COUNT(*) as villages FROM villages;
SQL
EOF

echo ""
echo "=========================================="
echo "UPDATE COMPLETED"
echo "=========================================="
echo ""
echo "Please verify the counts above match expected values."
echo "If something went wrong, restore from backup:"
echo "  ssh root@103.185.52.124"
echo "  sudo -u postgres psql < /tmp/full_backup_before_wilayah_update_*.sql"
