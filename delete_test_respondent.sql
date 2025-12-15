-- Delete test respondent
DELETE FROM respondents WHERE phone = '85719195627';

-- Check if deleted
SELECT COUNT(*) as remaining FROM respondents WHERE phone = '85719195627';
