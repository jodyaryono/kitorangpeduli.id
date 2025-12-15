--
-- PostgreSQL database dump
--

\restrict mWpRp1jWB9dXDt0YibI12aV39StYIb60s2wUBJDCiWTfUfAoBUjRMHlWhx0QjJf

-- Dumped from database version 14.20 (Ubuntu 14.20-0ubuntu0.22.04.1)
-- Dumped by pg_dump version 14.20 (Ubuntu 14.20-0ubuntu0.22.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: provinces; Type: TABLE DATA; Schema: public; Owner: admin_dev
--

INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (1, '11', 'ACEH', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (2, '12', 'SUMATERA UTARA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (3, '13', 'SUMATERA BARAT', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (4, '14', 'RIAU', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (5, '15', 'JAMBI', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (6, '16', 'SUMATERA SELATAN', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (7, '17', 'BENGKULU', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (8, '18', 'LAMPUNG', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (9, '19', 'KEPULAUAN BANGKA BELITUNG', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (10, '21', 'KEPULAUAN RIAU', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (11, '31', 'DKI JAKARTA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (12, '32', 'JAWA BARAT', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (13, '33', 'JAWA TENGAH', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (14, '34', 'DI YOGYAKARTA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (15, '35', 'JAWA TIMUR', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (16, '36', 'BANTEN', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (17, '51', 'BALI', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (18, '52', 'NUSA TENGGARA BARAT', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (19, '53', 'NUSA TENGGARA TIMUR', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (20, '61', 'KALIMANTAN BARAT', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (21, '62', 'KALIMANTAN TENGAH', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (22, '63', 'KALIMANTAN SELATAN', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (23, '64', 'KALIMANTAN TIMUR', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (24, '65', 'KALIMANTAN UTARA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (25, '71', 'SULAWESI UTARA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (26, '72', 'SULAWESI TENGAH', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (27, '73', 'SULAWESI SELATAN', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (28, '74', 'SULAWESI TENGGARA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (29, '75', 'GORONTALO', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (30, '76', 'SULAWESI BARAT', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (31, '81', 'MALUKU', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (32, '82', 'MALUKU UTARA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (33, '91', 'PAPUA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (34, '92', 'PAPUA BARAT', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (35, '93', 'PAPUA SELATAN', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (36, '94', 'PAPUA TENGAH', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (37, '95', 'PAPUA PEGUNUNGAN', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.provinces (id, code, name, created_at, updated_at) VALUES (38, '96', 'PAPUA BARAT DAYA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');


--
-- Data for Name: regencies; Type: TABLE DATA; Schema: public; Owner: admin_dev
--

INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (1, 33, '9101', 'KABUPATEN MERAUKE', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (2, 33, '9102', 'KABUPATEN JAYAWIJAYA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (3, 33, '9103', 'KABUPATEN JAYAPURA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (4, 33, '9104', 'KABUPATEN NABIRE', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (5, 33, '9105', 'KABUPATEN KEPULAUAN YAPEN', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (6, 33, '9106', 'KABUPATEN BIAK NUMFOR', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (7, 33, '9108', 'KABUPATEN PANIAI', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (8, 33, '9109', 'KABUPATEN PUNCAK JAYA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (9, 33, '9110', 'KABUPATEN MIMIKA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (10, 33, '9111', 'KABUPATEN BOVEN DIGOEL', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (11, 33, '9112', 'KABUPATEN MAPPI', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (12, 33, '9113', 'KABUPATEN ASMAT', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (13, 33, '9114', 'KABUPATEN YAHUKIMO', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (14, 33, '9115', 'KABUPATEN PEGUNUNGAN BINTANG', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (15, 33, '9116', 'KABUPATEN TOLIKARA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (16, 33, '9117', 'KABUPATEN SARMI', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (17, 33, '9118', 'KABUPATEN KEEROM', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (18, 33, '9119', 'KABUPATEN WAROPEN', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (19, 33, '9120', 'KABUPATEN SUPIORI', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (20, 33, '9121', 'KABUPATEN MAMBERAMO RAYA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (21, 33, '9122', 'KABUPATEN NDUGA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (22, 33, '9123', 'KABUPATEN LANNY JAYA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (23, 33, '9124', 'KABUPATEN MAMBERAMO TENGAH', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (24, 33, '9125', 'KABUPATEN YALIMO', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (25, 33, '9126', 'KABUPATEN PUNCAK', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (26, 33, '9127', 'KABUPATEN DOGIYAI', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (27, 33, '9128', 'KABUPATEN INTAN JAYA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (28, 33, '9129', 'KABUPATEN DEIYAI', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.regencies (id, province_id, code, name, created_at, updated_at) VALUES (29, 33, '9171', 'KOTA JAYAPURA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');


--
-- Data for Name: districts; Type: TABLE DATA; Schema: public; Owner: admin_dev
--

INSERT INTO public.districts (id, regency_id, code, name, created_at, updated_at) VALUES (1, 29, '9171010', 'ABEPURA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.districts (id, regency_id, code, name, created_at, updated_at) VALUES (2, 29, '9171011', 'HERAM', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.districts (id, regency_id, code, name, created_at, updated_at) VALUES (3, 29, '9171020', 'JAYAPURA SELATAN', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.districts (id, regency_id, code, name, created_at, updated_at) VALUES (4, 29, '9171021', 'JAYAPURA UTARA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.districts (id, regency_id, code, name, created_at, updated_at) VALUES (5, 29, '9171030', 'MUARA TAMI', '2025-12-11 14:33:22', '2025-12-11 14:33:22');


--
-- Data for Name: villages; Type: TABLE DATA; Schema: public; Owner: admin_dev
--

INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (1, 1, '9171010001', 'ABEPURA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (2, 1, '9171010002', 'ASANO', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (3, 1, '9171010003', 'KOTA BARU', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (4, 1, '9171010004', 'WAHNO', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (5, 1, '9171010005', 'YOBE', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (6, 1, '9171010006', 'VIM', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (7, 1, '9171010007', 'WAY MHOROCK', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (8, 1, '9171010008', 'AWIYO', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (9, 2, '9171011001', 'WAENA', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (10, 2, '9171011002', 'YABANSAI', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (11, 2, '9171011003', 'HEDAM', '2025-12-11 14:33:22', '2025-12-11 14:33:22');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (12, 2, '9171011004', 'WAENA SELATAN', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (13, 3, '9171020001', 'NUMBAY', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (14, 3, '9171020002', 'ENTROP', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (15, 3, '9171020003', 'HAMADI', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (16, 3, '9171020004', 'TOBATI', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (17, 3, '9171020005', 'ARD UJUNG', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (18, 3, '9171020006', 'VIM PANTAI', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (19, 4, '9171021001', 'TANJUNG RIA', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (20, 4, '9171021002', 'MANDALA', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (21, 4, '9171021003', 'IMBI', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (22, 4, '9171021004', 'ANGKASAPURA', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (23, 4, '9171021005', 'BHAYANGKARA', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (24, 4, '9171021006', 'GURABESI', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (25, 5, '9171030001', 'KOYA BARAT', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (26, 5, '9171030002', 'KOYA TIMUR', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (27, 5, '9171030003', 'KOYA TENGAH', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (28, 5, '9171030004', 'HOLTEKAMP', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (29, 5, '9171030005', 'SKOUW YAMBE', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (30, 5, '9171030006', 'SKOUW SAE', '2025-12-11 14:33:23', '2025-12-11 14:33:23');
INSERT INTO public.villages (id, district_id, code, name, created_at, updated_at) VALUES (31, 5, '9171030007', 'SKOUW MABO', '2025-12-11 14:33:23', '2025-12-11 14:33:23');


--
-- Name: districts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin_dev
--

SELECT pg_catalog.setval('public.districts_id_seq', 5, true);


--
-- Name: provinces_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin_dev
--

SELECT pg_catalog.setval('public.provinces_id_seq', 38, true);


--
-- Name: regencies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin_dev
--

SELECT pg_catalog.setval('public.regencies_id_seq', 29, true);


--
-- Name: villages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin_dev
--

SELECT pg_catalog.setval('public.villages_id_seq', 31, true);


--
-- PostgreSQL database dump complete
--

\unrestrict mWpRp1jWB9dXDt0YibI12aV39StYIb60s2wUBJDCiWTfUfAoBUjRMHlWhx0QjJf

