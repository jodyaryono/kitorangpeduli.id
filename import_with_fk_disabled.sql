-- Disable foreign key checks temporarily
SET session_replication_role = replica;

-- Import data
\i kitorangpeduli_backup.sql

-- Re-enable foreign key checks
SET session_replication_role = DEFAULT;
