-- Fix Data Wilayah - Papua Barat dan Yogyakarta
-- Masalah: Kabupaten Yogyakarta (3401-3471) salah masuk ke province_id 34 (Papua Barat)
-- Seharusnya masuk ke province_id yang benar untuk DI Yogyakarta

-- Step 1: Cek province ID untuk DI Yogyakarta
-- SELECT id, code, name FROM provinces WHERE name LIKE '%YOGYAKARTA%';
-- Hasil: Harusnya ada province dengan code '34' untuk DI Yogyakarta

-- Step 2: Lihat kabupaten yang salah
SELECT id, code, name, province_id
FROM regencies
WHERE code LIKE '34%'
ORDER BY code;

-- Step 3: Fix - Update province_id untuk kabupaten Yogyakarta
-- Kabupaten dengan code 34xx seharusnya berada di province code 34 (DI Yogyakarta)
-- Tapi saat ini mereka ada di province_id 34 yang salah

-- Cek dulu province mana yang code-nya '34' (DI Yogyakarta)
SELECT id, code, name FROM provinces WHERE code = '34';

-- Jika province code 34 adalah DI Yogyakarta, maka data sudah benar
-- Tapi kalau province_id 34 adalah Papua Barat, maka perlu diperbaiki

-- Solusi: Update regencies dengan code 34xx agar masuk ke province yang benar
-- UPDATE regencies
-- SET province_id = (SELECT id FROM provinces WHERE code = '34')
-- WHERE code LIKE '34%';

-- Step 4: Cek Papua Barat yang benar
SELECT id, code, name FROM provinces WHERE name LIKE '%PAPUA BARAT%' AND name NOT LIKE '%DAYA%';
-- Hasilnya: Papua Barat dengan code '92'

-- Step 5: Cek regencies Papua Barat yang seharusnya (code 92xx)
SELECT id, code, name, province_id
FROM regencies
WHERE code LIKE '92%'
ORDER BY code;

-- Jika regencies Papua Barat (92xx) belum ada di province Papua Barat,
-- maka perlu di-update juga
