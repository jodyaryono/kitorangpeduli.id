SELECT id, nama_lengkap, phone, nik, created_at, phone_verified_at
FROM respondents
WHERE phone LIKE '%81222475475%' OR phone = '81222475475' OR phone = '081222475475' OR phone = '6281222475475';
