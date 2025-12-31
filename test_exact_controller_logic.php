<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Resident;
use App\Models\Response;

$response = Response::find(7);

echo "Response ID: {$response->id}\n";
echo "Resident ID: {$response->resident_id}\n\n";

// Simulate the controller logic exactly
$familyId = null;

if ($response->resident && $response->resident->family_id) {
    $familyId = $response->resident->family_id;
    echo "✅ Family ID from response->resident: {$familyId}\n";
} else {
    echo "❌ No family_id from response->resident\n";

    $healthResponse = \App\Models\ResidentHealthResponse::where('response_id', $response->id)
        ->with('resident')
        ->first();

    if ($healthResponse && $healthResponse->resident && $healthResponse->resident->family_id) {
        $familyId = $healthResponse->resident->family_id;
        echo "✅ Family ID from health responses: {$familyId}\n";
    }
}

if ($familyId) {
    $savedResidents = \App\Models\Resident::where('family_id', $familyId)
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

    echo "\n📋 savedResidents array (as passed to view):\n";
    echo 'Count: ' . count($savedResidents) . "\n";
    echo 'Is empty: ' . (empty($savedResidents) ? 'YES' : 'NO') . "\n\n";

    if (!empty($savedResidents)) {
        echo "Data:\n";
        print_r($savedResidents);

        echo "\n\n🔄 Simulating Blade @foreach conversion:\n";
        $savedFamilyMembers = [];
        foreach ($savedResidents as $index => $resident) {
            $key = $index + 1;
            $savedFamilyMembers[$key] = $resident;
            echo "savedFamilyMembers[{$key}] = {$resident['nama_lengkap']} (ID: {$resident['id']})\n";
        }
    } else {
        echo "❌ savedResidents is EMPTY!\n";
    }
} else {
    echo "\n❌ No family_id found!\n";
}
