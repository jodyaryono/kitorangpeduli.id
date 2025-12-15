-- SAFE Production Schema Adjustment for Wilayah Import
-- Step 1: Temporarily allow NULL in code columns
-- Step 2: Import wilayah data
-- Step 3: Restore constraints

BEGIN;

-- Show current state
SELECT 'BEFORE ADJUSTMENT:' as status;
SELECT COUNT(*) as provinces FROM provinces;
SELECT COUNT(*) as regencies FROM regencies;
SELECT COUNT(*) as districts FROM districts;
SELECT COUNT(*) as villages FROM villages;

-- Step 1: Temporarily allow NULL (SAFE - does not delete data)
ALTER TABLE provinces ALTER COLUMN code DROP NOT NULL;
ALTER TABLE regencies ALTER COLUMN code DROP NOT NULL;
ALTER TABLE districts ALTER COLUMN code DROP NOT NULL;
ALTER TABLE villages ALTER COLUMN code DROP NOT NULL;

SELECT 'Schema adjusted - code columns now allow NULL' as status;

-- Step 2 will be done separately by importing wilayah_pg.sql

COMMIT;
