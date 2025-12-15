--
-- PostgreSQL database dump
--

\restrict LeepO1cw0eSzBe2TNEELuspR9cgoVuXS2QX9U1sMMzuOLDLVdOYZgSssE4Ac93o

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: districts; Type: TABLE; Schema: public; Owner: admin_dev
--

CREATE TABLE public.districts (
    id bigint NOT NULL,
    regency_id bigint NOT NULL,
    code character varying(7) NOT NULL,
    name character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.districts OWNER TO admin_dev;

--
-- Name: districts_id_seq; Type: SEQUENCE; Schema: public; Owner: admin_dev
--

CREATE SEQUENCE public.districts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.districts_id_seq OWNER TO admin_dev;

--
-- Name: districts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin_dev
--

ALTER SEQUENCE public.districts_id_seq OWNED BY public.districts.id;


--
-- Name: provinces; Type: TABLE; Schema: public; Owner: admin_dev
--

CREATE TABLE public.provinces (
    id bigint NOT NULL,
    code character varying(2) NOT NULL,
    name character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.provinces OWNER TO admin_dev;

--
-- Name: provinces_id_seq; Type: SEQUENCE; Schema: public; Owner: admin_dev
--

CREATE SEQUENCE public.provinces_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.provinces_id_seq OWNER TO admin_dev;

--
-- Name: provinces_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin_dev
--

ALTER SEQUENCE public.provinces_id_seq OWNED BY public.provinces.id;


--
-- Name: regencies; Type: TABLE; Schema: public; Owner: admin_dev
--

CREATE TABLE public.regencies (
    id bigint NOT NULL,
    province_id bigint NOT NULL,
    code character varying(4) NOT NULL,
    name character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.regencies OWNER TO admin_dev;

--
-- Name: regencies_id_seq; Type: SEQUENCE; Schema: public; Owner: admin_dev
--

CREATE SEQUENCE public.regencies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.regencies_id_seq OWNER TO admin_dev;

--
-- Name: regencies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin_dev
--

ALTER SEQUENCE public.regencies_id_seq OWNED BY public.regencies.id;


--
-- Name: villages; Type: TABLE; Schema: public; Owner: admin_dev
--

CREATE TABLE public.villages (
    id bigint NOT NULL,
    district_id bigint NOT NULL,
    code character varying(10) NOT NULL,
    name character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.villages OWNER TO admin_dev;

--
-- Name: villages_id_seq; Type: SEQUENCE; Schema: public; Owner: admin_dev
--

CREATE SEQUENCE public.villages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.villages_id_seq OWNER TO admin_dev;

--
-- Name: villages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin_dev
--

ALTER SEQUENCE public.villages_id_seq OWNED BY public.villages.id;


--
-- Name: districts id; Type: DEFAULT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.districts ALTER COLUMN id SET DEFAULT nextval('public.districts_id_seq'::regclass);


--
-- Name: provinces id; Type: DEFAULT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.provinces ALTER COLUMN id SET DEFAULT nextval('public.provinces_id_seq'::regclass);


--
-- Name: regencies id; Type: DEFAULT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.regencies ALTER COLUMN id SET DEFAULT nextval('public.regencies_id_seq'::regclass);


--
-- Name: villages id; Type: DEFAULT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.villages ALTER COLUMN id SET DEFAULT nextval('public.villages_id_seq'::regclass);


--
-- Data for Name: districts; Type: TABLE DATA; Schema: public; Owner: admin_dev
--

COPY public.districts (id, regency_id, code, name, created_at, updated_at) FROM stdin;
1	29	9171010	ABEPURA	2025-12-11 14:33:22	2025-12-11 14:33:22
2	29	9171011	HERAM	2025-12-11 14:33:22	2025-12-11 14:33:22
3	29	9171020	JAYAPURA SELATAN	2025-12-11 14:33:22	2025-12-11 14:33:22
4	29	9171021	JAYAPURA UTARA	2025-12-11 14:33:22	2025-12-11 14:33:22
5	29	9171030	MUARA TAMI	2025-12-11 14:33:22	2025-12-11 14:33:22
\.


--
-- Data for Name: provinces; Type: TABLE DATA; Schema: public; Owner: admin_dev
--

COPY public.provinces (id, code, name, created_at, updated_at) FROM stdin;
1	11	ACEH	2025-12-11 14:33:22	2025-12-11 14:33:22
2	12	SUMATERA UTARA	2025-12-11 14:33:22	2025-12-11 14:33:22
3	13	SUMATERA BARAT	2025-12-11 14:33:22	2025-12-11 14:33:22
4	14	RIAU	2025-12-11 14:33:22	2025-12-11 14:33:22
5	15	JAMBI	2025-12-11 14:33:22	2025-12-11 14:33:22
6	16	SUMATERA SELATAN	2025-12-11 14:33:22	2025-12-11 14:33:22
7	17	BENGKULU	2025-12-11 14:33:22	2025-12-11 14:33:22
8	18	LAMPUNG	2025-12-11 14:33:22	2025-12-11 14:33:22
9	19	KEPULAUAN BANGKA BELITUNG	2025-12-11 14:33:22	2025-12-11 14:33:22
10	21	KEPULAUAN RIAU	2025-12-11 14:33:22	2025-12-11 14:33:22
11	31	DKI JAKARTA	2025-12-11 14:33:22	2025-12-11 14:33:22
12	32	JAWA BARAT	2025-12-11 14:33:22	2025-12-11 14:33:22
13	33	JAWA TENGAH	2025-12-11 14:33:22	2025-12-11 14:33:22
14	34	DI YOGYAKARTA	2025-12-11 14:33:22	2025-12-11 14:33:22
15	35	JAWA TIMUR	2025-12-11 14:33:22	2025-12-11 14:33:22
16	36	BANTEN	2025-12-11 14:33:22	2025-12-11 14:33:22
17	51	BALI	2025-12-11 14:33:22	2025-12-11 14:33:22
18	52	NUSA TENGGARA BARAT	2025-12-11 14:33:22	2025-12-11 14:33:22
19	53	NUSA TENGGARA TIMUR	2025-12-11 14:33:22	2025-12-11 14:33:22
20	61	KALIMANTAN BARAT	2025-12-11 14:33:22	2025-12-11 14:33:22
21	62	KALIMANTAN TENGAH	2025-12-11 14:33:22	2025-12-11 14:33:22
22	63	KALIMANTAN SELATAN	2025-12-11 14:33:22	2025-12-11 14:33:22
23	64	KALIMANTAN TIMUR	2025-12-11 14:33:22	2025-12-11 14:33:22
24	65	KALIMANTAN UTARA	2025-12-11 14:33:22	2025-12-11 14:33:22
25	71	SULAWESI UTARA	2025-12-11 14:33:22	2025-12-11 14:33:22
26	72	SULAWESI TENGAH	2025-12-11 14:33:22	2025-12-11 14:33:22
27	73	SULAWESI SELATAN	2025-12-11 14:33:22	2025-12-11 14:33:22
28	74	SULAWESI TENGGARA	2025-12-11 14:33:22	2025-12-11 14:33:22
29	75	GORONTALO	2025-12-11 14:33:22	2025-12-11 14:33:22
30	76	SULAWESI BARAT	2025-12-11 14:33:22	2025-12-11 14:33:22
31	81	MALUKU	2025-12-11 14:33:22	2025-12-11 14:33:22
32	82	MALUKU UTARA	2025-12-11 14:33:22	2025-12-11 14:33:22
33	91	PAPUA	2025-12-11 14:33:22	2025-12-11 14:33:22
34	92	PAPUA BARAT	2025-12-11 14:33:22	2025-12-11 14:33:22
35	93	PAPUA SELATAN	2025-12-11 14:33:22	2025-12-11 14:33:22
36	94	PAPUA TENGAH	2025-12-11 14:33:22	2025-12-11 14:33:22
37	95	PAPUA PEGUNUNGAN	2025-12-11 14:33:22	2025-12-11 14:33:22
38	96	PAPUA BARAT DAYA	2025-12-11 14:33:22	2025-12-11 14:33:22
\.


--
-- Data for Name: regencies; Type: TABLE DATA; Schema: public; Owner: admin_dev
--

COPY public.regencies (id, province_id, code, name, created_at, updated_at) FROM stdin;
1	33	9101	KABUPATEN MERAUKE	2025-12-11 14:33:22	2025-12-11 14:33:22
2	33	9102	KABUPATEN JAYAWIJAYA	2025-12-11 14:33:22	2025-12-11 14:33:22
3	33	9103	KABUPATEN JAYAPURA	2025-12-11 14:33:22	2025-12-11 14:33:22
4	33	9104	KABUPATEN NABIRE	2025-12-11 14:33:22	2025-12-11 14:33:22
5	33	9105	KABUPATEN KEPULAUAN YAPEN	2025-12-11 14:33:22	2025-12-11 14:33:22
6	33	9106	KABUPATEN BIAK NUMFOR	2025-12-11 14:33:22	2025-12-11 14:33:22
7	33	9108	KABUPATEN PANIAI	2025-12-11 14:33:22	2025-12-11 14:33:22
8	33	9109	KABUPATEN PUNCAK JAYA	2025-12-11 14:33:22	2025-12-11 14:33:22
9	33	9110	KABUPATEN MIMIKA	2025-12-11 14:33:22	2025-12-11 14:33:22
10	33	9111	KABUPATEN BOVEN DIGOEL	2025-12-11 14:33:22	2025-12-11 14:33:22
11	33	9112	KABUPATEN MAPPI	2025-12-11 14:33:22	2025-12-11 14:33:22
12	33	9113	KABUPATEN ASMAT	2025-12-11 14:33:22	2025-12-11 14:33:22
13	33	9114	KABUPATEN YAHUKIMO	2025-12-11 14:33:22	2025-12-11 14:33:22
14	33	9115	KABUPATEN PEGUNUNGAN BINTANG	2025-12-11 14:33:22	2025-12-11 14:33:22
15	33	9116	KABUPATEN TOLIKARA	2025-12-11 14:33:22	2025-12-11 14:33:22
16	33	9117	KABUPATEN SARMI	2025-12-11 14:33:22	2025-12-11 14:33:22
17	33	9118	KABUPATEN KEEROM	2025-12-11 14:33:22	2025-12-11 14:33:22
18	33	9119	KABUPATEN WAROPEN	2025-12-11 14:33:22	2025-12-11 14:33:22
19	33	9120	KABUPATEN SUPIORI	2025-12-11 14:33:22	2025-12-11 14:33:22
20	33	9121	KABUPATEN MAMBERAMO RAYA	2025-12-11 14:33:22	2025-12-11 14:33:22
21	33	9122	KABUPATEN NDUGA	2025-12-11 14:33:22	2025-12-11 14:33:22
22	33	9123	KABUPATEN LANNY JAYA	2025-12-11 14:33:22	2025-12-11 14:33:22
23	33	9124	KABUPATEN MAMBERAMO TENGAH	2025-12-11 14:33:22	2025-12-11 14:33:22
24	33	9125	KABUPATEN YALIMO	2025-12-11 14:33:22	2025-12-11 14:33:22
25	33	9126	KABUPATEN PUNCAK	2025-12-11 14:33:22	2025-12-11 14:33:22
26	33	9127	KABUPATEN DOGIYAI	2025-12-11 14:33:22	2025-12-11 14:33:22
27	33	9128	KABUPATEN INTAN JAYA	2025-12-11 14:33:22	2025-12-11 14:33:22
28	33	9129	KABUPATEN DEIYAI	2025-12-11 14:33:22	2025-12-11 14:33:22
29	33	9171	KOTA JAYAPURA	2025-12-11 14:33:22	2025-12-11 14:33:22
\.


--
-- Data for Name: villages; Type: TABLE DATA; Schema: public; Owner: admin_dev
--

COPY public.villages (id, district_id, code, name, created_at, updated_at) FROM stdin;
1	1	9171010001	ABEPURA	2025-12-11 14:33:22	2025-12-11 14:33:22
2	1	9171010002	ASANO	2025-12-11 14:33:22	2025-12-11 14:33:22
3	1	9171010003	KOTA BARU	2025-12-11 14:33:22	2025-12-11 14:33:22
4	1	9171010004	WAHNO	2025-12-11 14:33:22	2025-12-11 14:33:22
5	1	9171010005	YOBE	2025-12-11 14:33:22	2025-12-11 14:33:22
6	1	9171010006	VIM	2025-12-11 14:33:22	2025-12-11 14:33:22
7	1	9171010007	WAY MHOROCK	2025-12-11 14:33:22	2025-12-11 14:33:22
8	1	9171010008	AWIYO	2025-12-11 14:33:22	2025-12-11 14:33:22
9	2	9171011001	WAENA	2025-12-11 14:33:22	2025-12-11 14:33:22
10	2	9171011002	YABANSAI	2025-12-11 14:33:22	2025-12-11 14:33:22
11	2	9171011003	HEDAM	2025-12-11 14:33:22	2025-12-11 14:33:22
12	2	9171011004	WAENA SELATAN	2025-12-11 14:33:23	2025-12-11 14:33:23
13	3	9171020001	NUMBAY	2025-12-11 14:33:23	2025-12-11 14:33:23
14	3	9171020002	ENTROP	2025-12-11 14:33:23	2025-12-11 14:33:23
15	3	9171020003	HAMADI	2025-12-11 14:33:23	2025-12-11 14:33:23
16	3	9171020004	TOBATI	2025-12-11 14:33:23	2025-12-11 14:33:23
17	3	9171020005	ARD UJUNG	2025-12-11 14:33:23	2025-12-11 14:33:23
18	3	9171020006	VIM PANTAI	2025-12-11 14:33:23	2025-12-11 14:33:23
19	4	9171021001	TANJUNG RIA	2025-12-11 14:33:23	2025-12-11 14:33:23
20	4	9171021002	MANDALA	2025-12-11 14:33:23	2025-12-11 14:33:23
21	4	9171021003	IMBI	2025-12-11 14:33:23	2025-12-11 14:33:23
22	4	9171021004	ANGKASAPURA	2025-12-11 14:33:23	2025-12-11 14:33:23
23	4	9171021005	BHAYANGKARA	2025-12-11 14:33:23	2025-12-11 14:33:23
24	4	9171021006	GURABESI	2025-12-11 14:33:23	2025-12-11 14:33:23
25	5	9171030001	KOYA BARAT	2025-12-11 14:33:23	2025-12-11 14:33:23
26	5	9171030002	KOYA TIMUR	2025-12-11 14:33:23	2025-12-11 14:33:23
27	5	9171030003	KOYA TENGAH	2025-12-11 14:33:23	2025-12-11 14:33:23
28	5	9171030004	HOLTEKAMP	2025-12-11 14:33:23	2025-12-11 14:33:23
29	5	9171030005	SKOUW YAMBE	2025-12-11 14:33:23	2025-12-11 14:33:23
30	5	9171030006	SKOUW SAE	2025-12-11 14:33:23	2025-12-11 14:33:23
31	5	9171030007	SKOUW MABO	2025-12-11 14:33:23	2025-12-11 14:33:23
\.


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
-- Name: districts districts_code_unique; Type: CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.districts
    ADD CONSTRAINT districts_code_unique UNIQUE (code);


--
-- Name: districts districts_pkey; Type: CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.districts
    ADD CONSTRAINT districts_pkey PRIMARY KEY (id);


--
-- Name: provinces provinces_code_unique; Type: CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.provinces
    ADD CONSTRAINT provinces_code_unique UNIQUE (code);


--
-- Name: provinces provinces_pkey; Type: CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.provinces
    ADD CONSTRAINT provinces_pkey PRIMARY KEY (id);


--
-- Name: regencies regencies_code_unique; Type: CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.regencies
    ADD CONSTRAINT regencies_code_unique UNIQUE (code);


--
-- Name: regencies regencies_pkey; Type: CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.regencies
    ADD CONSTRAINT regencies_pkey PRIMARY KEY (id);


--
-- Name: villages villages_code_unique; Type: CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.villages
    ADD CONSTRAINT villages_code_unique UNIQUE (code);


--
-- Name: villages villages_pkey; Type: CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.villages
    ADD CONSTRAINT villages_pkey PRIMARY KEY (id);


--
-- Name: districts_regency_id_index; Type: INDEX; Schema: public; Owner: admin_dev
--

CREATE INDEX districts_regency_id_index ON public.districts USING btree (regency_id);


--
-- Name: regencies_province_id_index; Type: INDEX; Schema: public; Owner: admin_dev
--

CREATE INDEX regencies_province_id_index ON public.regencies USING btree (province_id);


--
-- Name: villages_district_id_index; Type: INDEX; Schema: public; Owner: admin_dev
--

CREATE INDEX villages_district_id_index ON public.villages USING btree (district_id);


--
-- Name: districts districts_regency_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.districts
    ADD CONSTRAINT districts_regency_id_foreign FOREIGN KEY (regency_id) REFERENCES public.regencies(id) ON DELETE CASCADE;


--
-- Name: regencies regencies_province_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.regencies
    ADD CONSTRAINT regencies_province_id_foreign FOREIGN KEY (province_id) REFERENCES public.provinces(id) ON DELETE CASCADE;


--
-- Name: villages villages_district_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin_dev
--

ALTER TABLE ONLY public.villages
    ADD CONSTRAINT villages_district_id_foreign FOREIGN KEY (district_id) REFERENCES public.districts(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict LeepO1cw0eSzBe2TNEELuspR9cgoVuXS2QX9U1sMMzuOLDLVdOYZgSssE4Ac93o

