<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Resident;
use App\Models\Response;

// Simulate what the controller does
$response = Response::find(2);

if (!$response) {
    echo "❌ Response not found\n";
    exit;
}

echo "✅ Response ID: {$response->id}\n";
echo "✅ Resident ID: {$response->resident_id}\n\n";

if (!$response->resident) {
    echo "❌ No resident linked\n";
    exit;
}

$resident = $response->resident;
echo "✅ Resident: {$resident->nama_lengkap}\n";
echo "✅ Family ID: {$resident->family_id}\n\n";

// Load saved residents data (same as controller)
$savedResidents = [];
if ($response->resident && $response->resident->family_id) {
    $savedResidents = \App\Models\Resident::where('family_id', $response->resident->family_id)
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
}

echo "📋 savedResidents array:\n";
echo json_encode($savedResidents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Simulate JavaScript conversion
echo "🔄 JavaScript conversion (savedFamilyMembers):\n";
$savedFamilyMembers = [];
foreach ($savedResidents as $index => $resident) {
    $savedFamilyMembers[$index + 1] = $resident;
}
echo json_encode($savedFamilyMembers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
