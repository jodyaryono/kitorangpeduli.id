<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Resident;
use App\Models\Response;

// Get the latest response
$response = Response::latest()->first();

if (!$response) {
    echo "❌ No response found\n";
    exit;
}

echo "✅ Response ID: {$response->id}\n";
echo "✅ Resident ID: {$response->resident_id}\n";

$resident = $response->resident;
if (!$resident) {
    echo "❌ No resident found for this response\n";
    exit;
}

echo "✅ Resident Name: {$resident->nama_lengkap}\n";
echo "✅ Family ID: {$resident->family_id}\n\n";

// Get all residents with the same family_id
$familyResidents = Resident::where('family_id', $resident->family_id)->get();

echo '📋 Total residents in family: ' . $familyResidents->count() . "\n\n";

foreach ($familyResidents as $index => $famResident) {
    echo '👤 Resident ' . ($index + 1) . ":\n";
    echo "   - ID: {$famResident->id}\n";
    echo "   - NIK: {$famResident->nik}\n";
    echo "   - Nama: {$famResident->nama_lengkap}\n";
    echo "   - Hubungan ID: {$famResident->relationship_id}\n";
    echo "   - Gender ID: {$famResident->gender_id}\n";
    echo "   - DOB: {$famResident->date_of_birth}\n";
    echo "   - Phone: {$famResident->phone}\n\n";
}

// Check savedResidents structure
echo "\n🔍 Testing savedResidents format:\n\n";
$savedResidents = Resident::where('family_id', $resident->family_id)
    ->get()
    ->map(function ($resident, $index) {
        return [
            'id' => $resident->id,
            'nik' => $resident->nik,
            'citizen_type_id' => $resident->citizen_type_id,
            'nama_lengkap' => $resident->nama_lengkap,
            'hubungan' => $resident->relationship_id,
            'tempat_lahir' => $resident->place_of_birth,
            'tanggal_lahir' => $resident->date_of_birth ? \Carbon\Carbon::parse($resident->date_of_birth)->format('d/m/Y') : null,
            'jenis_kelamin' => $resident->gender_id,
            'status_perkawinan' => $resident->marital_status_id,
            'agama' => $resident->religion_id,
            'pendidikan' => $resident->education_id,
            'pekerjaan' => $resident->occupation_id,
            'golongan_darah' => $resident->blood_type_id,
            'phone' => $resident->phone,
            'ktp_image_path' => $resident->ktp_image_path,
            'ktp_kia_path' => $resident->ktp_kia_path,
            'umur' => $resident->date_of_birth ? \Carbon\Carbon::parse($resident->date_of_birth)->age : null,
        ];
    })
    ->values()
    ->toArray();

echo json_encode($savedResidents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
