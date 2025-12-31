# STRUKTUR PENYIMPANAN JAWABAN KUESIONER

## 📊 3 TABEL UTAMA

### 1️⃣ **Tabel: `answers`**

**Menyimpan:** Jawaban untuk pertanyaan reguler/umum

**Kolom Penting:**

-   `id` - Primary key
-   `response_id` - FK ke tabel responses (submission mana)
-   `question_id` - FK ke tabel questions (pertanyaan apa)
-   `answer_text` - Jawaban berupa text/string
-   `selected_options` - Jawaban pilihan ganda (JSON)
-   `media_path` - Path file upload (jika ada)
-   `resident_id` - FK ke residents (untuk pertanyaan per anggota)

**Contoh Data (Response #11):**

```
ID  | response_id | question_id | answer_text
----|-------------|-------------|-------------
48  | 11          | 277         | Ya
49  | 11          | 278         | Ya
50  | 11          | 279         | Ya
```

**Total Records:** 7

---

### 2️⃣ **Tabel: `resident_health_responses`**

**Menyimpan:** Jawaban kesehatan per anggota keluarga (Section V)

**Kolom Penting:**

-   `id` - Primary key
-   `resident_id` - FK ke residents (anggota keluarga mana)
-   `response_id` - FK ke responses (submission mana)
-   `question_code` - Kode pertanyaan (jkn, merokok, jamban, dll)
-   `answer` - Jawaban (biasanya 1/2 untuk Ya/Tidak)

**Contoh Data (Billy Wasom - Resident #26):**

```
ID  | resident_id | response_id | question_code | answer
----|-------------|-------------|---------------|--------
64  | 26          | 11          | jkn           | 1
65  | 26          | 11          | merokok       | 1
66  | 26          | 11          | jamban        | 1
```

**Total Records:** 13

---

### 3️⃣ **Tabel: `responses`**

**Menyimpan:** Metadata submission kuesioner

**Kolom Penting:**

-   `id` - Primary key (Response ID)
-   `questionnaire_id` - FK ke questionnaires (kuesioner mana)
-   `resident_id` - FK ke residents (responden/kepala keluarga)
-   `status` - Status: in_progress / completed / rejected
-   `entered_by_user_id` - FK ke users (officer yang input)
-   `started_at` - Waktu mulai
-   `completed_at` - Waktu selesai
-   `officer_notes` - Catatan officer

**Contoh Data:**

```
ID  | questionnaire_id | resident_id | status      | entered_by_user_id
----|------------------|-------------|-------------|-------------------
11  | 8                | 26          | in_progress | 1
```

**Total Records:** 1

---

## 🔄 RELASI ANTAR TABEL

```
responses (1 submission)
    ├── answers (banyak jawaban reguler)
    │   └── question_id → questions (pertanyaan apa)
    │
    ├── resident_health_responses (banyak jawaban kesehatan)
    │   ├── resident_id → residents (anggota keluarga)
    │   └── question_code (jkn, merokok, dll)
    │
    └── resident_id → residents (responden/kepala keluarga)
        └── family_id → families (data keluarga)
```

---

## 📝 CONTOH QUERY

### Query 1: Ambil semua jawaban untuk Response #11

```sql
-- Jawaban reguler
SELECT q.question_text, a.answer_text, a.selected_options
FROM answers a
JOIN questions q ON a.question_id = q.id
WHERE a.response_id = 11;

-- Jawaban kesehatan
SELECT r.nama_lengkap, h.question_code, h.answer
FROM resident_health_responses h
JOIN residents r ON h.resident_id = r.id
WHERE h.response_id = 11;
```

### Query 2: Lihat status response

```sql
SELECT
    r.id,
    r.status,
    res.nama_lengkap as responden,
    u.name as officer,
    COUNT(DISTINCT a.id) as total_jawaban_reguler,
    COUNT(DISTINCT h.id) as total_jawaban_kesehatan
FROM responses r
LEFT JOIN residents res ON r.resident_id = res.id
LEFT JOIN users u ON r.entered_by_user_id = u.id
LEFT JOIN answers a ON r.id = a.response_id
LEFT JOIN resident_health_responses h ON r.id = h.response_id
WHERE r.id = 11
GROUP BY r.id, res.nama_lengkap, u.name;
```

---

## 🎯 PERBEDAAN KEDUA TABEL JAWABAN

| Aspek            | `answers`                | `resident_health_responses`      |
| ---------------- | ------------------------ | -------------------------------- |
| **Untuk**        | Pertanyaan umum/keluarga | Pertanyaan kesehatan per anggota |
| **Scope**        | 1 jawaban per response   | Banyak jawaban (per anggota)     |
| **Identifikasi** | question_id (number)     | question_code (string)           |
| **Relasi**       | response → question      | response → resident + code       |
| **Contoh**       | Alamat, RT/RW, No KK     | JKN, Merokok, Jamban             |

---

## 📊 STATUS CURRENT

**Response #11:**

-   ✅ Total jawaban reguler: **7** (di tabel `answers`)
-   ✅ Total jawaban kesehatan: **13** (di tabel `resident_health_responses`)
-   👤 Responden: **Billy Wasom** (Resident #26)
-   👨‍💼 Officer: **Administrator** (User #1)
-   📝 Status: **in_progress**

---

## 🔍 CARA CEK JAWABAN

Jalankan script:

```bash
php show_answer_tables.php
```

Atau query langsung:

```bash
# Lihat jawaban reguler
php artisan tinker
>>> \App\Models\Answer::where('response_id', 11)->get();

# Lihat jawaban kesehatan
>>> \App\Models\ResidentHealthResponse::where('response_id', 11)->get();
```

---

**Updated:** 2025-12-31
