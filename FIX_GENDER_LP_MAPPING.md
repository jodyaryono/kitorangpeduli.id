# PERBAIKAN FIELD MAPPING: 'L'/'P' untuk Jenis Kelamin

## Masalah yang Ditemukan

1. **Tabel anggota keluarga** menampilkan "-" untuk:

    - Status Keluarga
    - Jenis Kelamin
    - Umur

2. **Root Cause**:
    - Database PostgreSQL constraint: `jenis_kelamin` harus 'L' atau 'P', BUKAN 1 atau 2
    - Controller dan blade template menggunakan mapping yang salah
    - Modal form menyimpan value 1/2 bukan 'L'/'P'

## Perbaikan yang Dilakukan

### 1. QuestionnaireController.php (Lines 155-178)

**SEBELUM:**

```php
'hubungan' => $resident->relationship_id,  // ❌ Column tidak ada
'jenis_kelamin' => $resident->gender_id,   // ❌ Column tidak ada, value 1/2
'tanggal_lahir' => $resident->date_of_birth, // ❌ Column tidak ada
```

**SESUDAH:**

```php
'hubungan' => $resident->family_relation_id,  // ✅ Correct column name
'jenis_kelamin' => $resident->jenis_kelamin,  // ✅ Correct column, value 'L'/'P'
'tanggal_lahir' => $resident->tanggal_lahir,  // ✅ Correct column name
'tempat_lahir' => $resident->tempat_lahir,
'golongan_darah' => $resident->golongan_darah,
```

### 2. fill.blade.php - Modal Form (Lines 804-808)

**SEBELUM:**

```html
<option value="1">1. Pria</option>
<option value="2">2. Wanita</option>
```

**SESUDAH:**

```html
<option value="L">L. Laki-laki</option>
<option value="P">P. Perempuan</option>
```

### 3. fill.blade.php - Table Display (Line 1478)

**SEBELUM:**

```javascript
const jenisKelaminText =
    memberData.jenis_kelamin === "1"
        ? "L"
        : memberData.jenis_kelamin === "2"
        ? "P"
        : "-";
```

**SESUDAH:**

```javascript
const jenisKelaminText =
    memberData.jenis_kelamin === "L"
        ? "L"
        : memberData.jenis_kelamin === "P"
        ? "P"
        : "-";
```

### 4. fill.blade.php - Conditional Health Questions (Line 2215)

**SEBELUM:**

```javascript
if ((memberGender === '2' && memberAge >= 10 && memberAge <= 54) ||
    (memberGender === '1' && memberAge >= 10)) {
```

**SESUDAH:**

```javascript
if ((memberGender === 'P' && memberAge >= 10 && memberAge <= 54) ||
    (memberGender === 'L' && memberAge >= 10)) {
```

### 5. fill.blade.php - Gender Label (Line 2216)

**SEBELUM:**

```javascript
const genderLabel = memberGender === "2" ? "wanita..." : "laki-laki...";
```

**SESUDAH:**

```javascript
const genderLabel = memberGender === "P" ? "wanita..." : "laki-laki...";
```

### 6. fill.blade.php - Question 12 Conditional (Line 2240)

**SEBELUM:**

```javascript
if (memberGender === '2' && memberAge >= 15 && memberAge <= 54) {
```

**SESUDAH:**

```javascript
if (memberGender === 'P' && memberAge >= 15 && memberAge <= 54) {
```

## Database Schema

```sql
-- residents table constraint
CHECK (jenis_kelamin = ANY (ARRAY['L', 'P']))

-- Valid values:
-- 'L' = Laki-laki
-- 'P' = Perempuan
```

## Test Data (residents id=21)

```
nik: 1234567890123456
nama_lengkap: BILLY WASOM
family_relation_id: 1 (Kepala Keluarga)
jenis_kelamin: L (Laki-laki)
tanggal_lahir: 1985-05-15
tempat_lahir: Jayapura
umur: 40 tahun (calculated)
```

## Expected Result After Fix

### ✅ Tabel Anggota Keluarga

| No  | NIK              | Nama        | Status Keluarga | Jenis Kelamin | Umur |
| --- | ---------------- | ----------- | --------------- | ------------- | ---- |
| 1   | 1234567890123456 | BILLY WASOM | Kepala Keluarga | L             | 40   |

### ✅ Form Auto-Fill

-   Provinsi: PAPUA (id=94)
-   Kabupaten: KOTA JAYAPURA (id=9471)
-   Kecamatan: JAYAPURA UTARA (id=9471040)
-   Desa: IMBI (id=33)
-   RT: 002
-   RW: 003
-   No. Bangunan: 006
-   No. KK: 006
-   Alamat: DOK VIII ATAS

### ✅ KK Image Preview

-   Path: `questionnaire_uploads/Xd2dp4DTaEZtOWjyoShYVF2AhyYmp6Bxj19yG0zw.jpg`
-   Status: SHOWN

### ✅ Health Questions (Questions 11-15)

-   Question 11: Will show for BILLY (male, age 40 ≥ 10)
-   Question 12: Will NOT show (only for female age 15-54)

## Catatan Penting

1. **SEMUA anggota keluarga baru** yang ditambahkan via modal akan otomatis menyimpan 'L' atau 'P'
2. **Conditional health questions** sekarang bekerja dengan benar berdasarkan gender dan umur
3. **Display di tabel** sekarang menampilkan 'L' dan 'P' dengan benar
4. **Database constraint** sekarang tidak akan error lagi saat save

## Files Modified

1. `app/Http/Controllers/QuestionnaireController.php` (Lines 155-178)
2. `resources/views/questionnaire/fill.blade.php` (Lines 804-808, 1478, 2213, 2215-2216, 2240)

## Testing Checklist

-   [ ] Refresh halaman /officer/questionnaire/8/fill/7
-   [ ] Verify tabel shows: No=1, NIK, Nama, Status Keluarga, Jenis Kelamin=L, Umur=40
-   [ ] Verify form fields auto-filled with wilayah, RT, RW, alamat, etc.
-   [ ] Verify KK image preview shown
-   [ ] Click "Tambah Anggota Keluarga"
-   [ ] Add new member with Jenis Kelamin=P
-   [ ] Verify saves successfully
-   [ ] Click "Pilih" for BILLY (male, 40)
-   [ ] Verify Question 11 shows
-   [ ] Verify Question 12 does NOT show
-   [ ] Add new female member age 30
-   [ ] Click "Pilih" for her
-   [ ] Verify Questions 11 and 12 both show
