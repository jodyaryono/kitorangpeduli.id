-- Fix regencies province_id to match production province IDs
-- Development: Papua Barat=91, Papua=94
-- Production: Papua Barat=91, Papua=94
-- Regencies 91xx belong to Papua Barat (91)
-- Regencies 94xx belong to Papua (94)

-- Papua Barat regencies (91xx -> province 91)
UPDATE regencies SET province_id = '91' WHERE id >= 9100 AND id < 9200;

-- Papua regencies (94xx -> province 94)
UPDATE regencies SET province_id = '94' WHERE id >= 9400 AND id < 9500;
