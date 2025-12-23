# Question Types - Wilayah Cascade & Lookup

## 📍 Tipe Pertanyaan Wilayah (Cascading Dropdowns)

Sistem sekarang mendukung tipe pertanyaan khusus untuk data wilayah administratif yang bisa cascade otomatis:

### Available Types:

1. **`province`** - 🗺️ Provinsi

    - Dropdown dari semua provinsi di Indonesia
    - Data source: `provinces` table
    - API: `GET /api/wilayah/provinces`

2. **`regency`** - 🏙️ Kabupaten/Kota

    - Dropdown kabupaten/kota filtered by provinsi
    - Data source: `regencies` table
    - API: `GET /api/wilayah/regencies/{province}`
    - Settings: `cascades_from_question_id` → ID pertanyaan provinsi

3. **`district`** - 🏘️ Kecamatan

    - Dropdown kecamatan filtered by kabupaten
    - Data source: `districts` table
    - API: `GET /api/wilayah/districts/{regency}`
    - Settings: `cascades_from_question_id` → ID pertanyaan kabupaten

4. **`village`** - 🏠 Kelurahan/Desa
    - Dropdown kelurahan/desa filtered by kecamatan
    - Data source: `villages` table
    - API: `GET /api/wilayah/villages/{district}`
    - Settings: `cascades_from_question_id` → ID pertanyaan kecamatan

### Cara Menggunakan di Seeder:

```php
// 1. Create Province question
$provinceQuestion = Question::create([
    'questionnaire_id' => $questionnaire->id,
    'question_text' => 'Provinsi',
    'question_type' => 'province',
    'order' => 1,
    'is_required' => true,
]);

// 2. Create Regency question (cascade from Province)
$regencyQuestion = Question::create([
    'questionnaire_id' => $questionnaire->id,
    'question_text' => 'Kabupaten/Kota',
    'question_type' => 'regency',
    'order' => 2,
    'is_required' => true,
    'settings' => ['cascades_from_question_id' => $provinceQuestion->id],
]);

// 3. Create District question (cascade from Regency)
$districtQuestion = Question::create([
    'questionnaire_id' => $questionnaire->id,
    'question_text' => 'Kecamatan',
    'question_type' => 'district',
    'order' => 3,
    'is_required' => true,
    'settings' => ['cascades_from_question_id' => $regencyQuestion->id],
]);

// 4. Create Village question (cascade from District)
Question::create([
    'questionnaire_id' => $questionnaire->id,
    'question_text' => 'Kelurahan/Desa',
    'question_type' => 'village',
    'order' => 4,
    'is_required' => true,
    'settings' => ['cascades_from_question_id' => $districtQuestion->id],
]);
```

---

## 🔍 Tipe Pertanyaan Lookup (Create on-the-fly)

Untuk dropdown dengan data master yang bisa ditambah on-the-fly oleh user:

### Available Types:

1. **`puskesmas`** - 🏥 Puskesmas (Lookup)

    - Dropdown puskesmas filtered by kabupaten
    - Data source: `puskesmas` table
    - API GET: `GET /api/puskesmas?regency_id={id}`
    - API CREATE: `POST /api/puskesmas` (authenticated)
    - Settings: `lookup_model: 'Puskesmas'`
    - User bisa tambah puskesmas baru jika tidak ada di list

2. **`field_officer`** - 👮 Petugas Lapangan (Lookup)

    - Dropdown petugas lapangan/pencacah filtered by OPD
    - Data source: `users` table dengan `role = 'field_officer'`
    - API GET: `GET /api/field-officers?opd_id={id}`
    - API SHOW: `GET /api/field-officers/{id}`
    - Settings: `lookup_model: 'User'`
    - Read-only (tidak bisa create on-the-fly)

3. **`lookup`** - 🔍 Lookup Generic (Creatable)
    - Dropdown generic untuk master data lain (Sekolah, Posyandu, dll)
    - Settings: `lookup_model: 'NamaModel'` (custom)
    - Perlu API endpoint custom untuk model tersebut

### Cara Menggunakan Puskesmas:

```php
Question::create([
    'questionnaire_id' => $questionnaire->id,
    'question_text' => 'Nama Puskesmas',
    'question_type' => 'puskesmas',
    'order' => 6,
    'is_required' => true,
    'settings' => ['lookup_model' => 'Puskesmas'],
]);
```

### Cara Menggunakan Field Officer:

```php
Question::create([
    'questionnaire_id' => $questionnaire->id,
    'question_text' => 'Nama Pencacah',
    'question_type' => 'field_officer',
    'order' => 21,
    'is_required' => true,
    'settings' => ['lookup_model' => 'User'],
]);
```

### API untuk Create Puskesmas on-the-fly:

```bash
POST /api/puskesmas
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Puskesmas Jayapura",
  "regency_id": "9401",
  "code": "P001",
  "address": "Jl. Kesehatan No.1",
  "phone": "0967123456"
}
```

Response:

```json
{
  "success": true,
  "data": {
    "id": 123,
    "name": "Puskesmas Jayapura",
    "regency_id": "9401",
    ...
  },
  "message": "Puskesmas berhasil ditambahkan"
}
```

---

## 📋 Migration Check Constraint

Database sudah di-update untuk support tipe baru:

```sql
ALTER TABLE questions
ADD CONSTRAINT questions_question_type_check
CHECK (question_type IN (
    'text', 'textarea', 'single_choice', 'multiple_choice',
    'dropdown', 'scale', 'date', 'file', 'image', 'video', 'location',
    'province', 'regency', 'district', 'village', 'puskesmas', 'field_officer', 'lookup'
));
```

Migration: `2025_12_20_074052_add_wilayah_and_lookup_question_types.php`

---

## 🎨 UI Implementation (Frontend)

Frontend perlu implement:

### 1. Cascading Dropdowns untuk Wilayah

```javascript
// When user selects province
onProvinceChange(provinceId) {
  // Clear dependent fields
  this.regencyId = null;
  this.districtId = null;
  this.villageId = null;

  // Load regencies
  axios.get(`/api/wilayah/regencies/${provinceId}`)
    .then(res => this.regencies = res.data.data);
}

// Similar for regency → districts → villages
```

### 2. Creatable Select untuk Puskesmas/Lookup

```javascript
// React-select with creatable
<CreatableSelect
    options={puskesmasList}
    onCreateOption={handleCreatePuskesmas}
    onChange={handleSelectPuskesmas}
/>;

async function handleCreatePuskesmas(inputValue) {
    const newPuskesmas = await axios.post("/api/puskesmas", {
        name: inputValue,
        regency_id: currentRegencyId,
    });

    // Add to options and select it
    setPuskesmasList([...puskesmasList, newPuskesmas.data.data]);
    setSelectedPuskesmas(newPuskesmas.data.data);
}
```

### 3. Regular Select untuk Field Officer (Read-only)

```javascript
// Regular select for field officers
<Select
    options={fieldOfficersList}
    onChange={handleSelectFieldOfficer}
    placeholder="Pilih Petugas Lapangan"
/>;

// Load field officers by OPD
useEffect(() => {
    if (opdId) {
        axios.get(`/api/field-officers?opd_id=${opdId}`).then((res) =>
            setFieldOfficersList(
                res.data.data.map((officer) => ({
                    value: officer.id,
                    label: officer.name,
                }))
            )
        );
    }
}, [opdId]);
```

---

## ✅ Status

-   ✅ Question types added to model
-   ✅ Database constraint updated
-   ✅ API endpoints created (WilayahController, PuskesmasController)
-   ✅ Routes configured
-   ✅ Seeder updated to use new types
-   ⏳ Frontend implementation (pending)

---

## 🔧 Troubleshooting

### Error: Check constraint violation

```
SQLSTATE[23514]: Check violation: questions_question_type_check
```

**Solution:** Run migration untuk update constraint:

```bash
php artisan migrate
```

### Cascade tidak bekerja

-   Pastikan `settings.cascades_from_question_id` berisi ID pertanyaan parent yang benar
-   Pastikan order pertanyaan sudah benar (parent harus lebih dulu)

### Create puskesmas gagal

-   Pastikan user sudah authenticated
-   Pastikan `regency_id` valid dan exists di table regencies
