-- Safe wilayah update script for PRODUCTION
-- This will ONLY INSERT new data, will NOT delete or update existing data
-- Date: 2025-12-15

-- Start transaction for safety
BEGIN;

-- Show current counts BEFORE
SELECT 'BEFORE UPDATE:' as status;
SELECT COUNT(*) as provinces FROM provinces;
SELECT COUNT(*) as regencies FROM regencies;
SELECT COUNT(*) as districts FROM districts;
SELECT COUNT(*) as villages FROM villages;

-- Import from local dump (will use ON CONFLICT DO NOTHING in actual execution)
-- This section will be populated by the actual SQL dump

-- Show current counts AFTER
SELECT 'AFTER UPDATE:' as status;
SELECT COUNT(*) as provinces FROM provinces;
SELECT COUNT(*) as regencies FROM regencies;
SELECT COUNT(*) as districts FROM districts;
SELECT COUNT(*) as villages FROM villages;

-- IMPORTANT: Review the counts above
-- If everything looks good, type COMMIT;
-- If something is wrong, type ROLLBACK;

-- Waiting for manual review...
-- DO NOT AUTO-COMMIT
