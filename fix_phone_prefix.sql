-- Fix phone numbers without 62 prefix
UPDATE respondents
SET phone = '62' || phone
WHERE phone NOT LIKE '62%' AND phone ~ '^[0-9]+$';

-- Verify the update
SELECT id, nama_lengkap, phone FROM respondents WHERE id = 205;
