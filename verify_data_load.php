<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== VERIFYING DATA THAT WILL BE LOADED ===\n\n";

// Check response
$response = DB::table('responses')->where('id', 7)->first();
echo "RESPONSE ID: 7\n";
echo '  resident_id: ' . ($response->resident_id ?? 'NULL') . "\n";
echo '  status: ' . ($response->status ?? 'NULL') . "\n";
echo '  notes: ' . ($response->notes ?? 'NULL') . "\n";
echo '  officer_notes: ' . ($response->officer_notes ?? 'NULL') . "\n\n";

// Check family via resident
if ($response->resident_id) {
    $resident = DB::table('residents')->where('id', $response->resident_id)->first();
    echo "RESIDENT (from response->resident_id):\n";
    echo '  id: ' . $resident->id . "\n";
    echo '  nama_lengkap: ' . $resident->nama_lengkap . "\n";
    echo '  family_id: ' . $resident->family_id . "\n\n";

    $family = DB::table('families')->where('id', $resident->family_id)->first();
} else {
    echo "WARNING: resident_id is NULL, finding via respondent_id\n";
    $family = DB::table('families')
        ->where('head_resident_id', $response->respondent_id)
        ->first();
}

if ($family) {
    echo "FAMILY DATA (will be loaded as savedFamily):\n";
    echo '  id: ' . $family->id . "\n";
    echo '  no_kk: ' . ($family->no_kk ?? 'NULL') . "\n";
    echo '  alamat: ' . ($family->alamat ?? 'NULL') . "\n";
    echo '  rt: ' . ($family->rt ?? 'NULL') . "\n";
    echo '  rw: ' . ($family->rw ?? 'NULL') . "\n";
    echo '  no_bangunan: ' . ($family->no_bangunan ?? 'NULL') . "\n";
    echo '  province_id: ' . ($family->province_id ?? 'NULL') . "\n";
    echo '  regency_id: ' . ($family->regency_id ?? 'NULL') . "\n";
    echo '  district_id: ' . ($family->district_id ?? 'NULL') . "\n";
    echo '  village_id: ' . ($family->village_id ?? 'NULL') . "\n";
    echo '  puskesmas_id: ' . ($family->puskesmas_id ?? 'NULL') . "\n";
    echo '  kk_image_path: ' . ($family->kk_image_path ?? 'NULL') . "\n\n";

    // Get family members
    echo "FAMILY MEMBERS (will be loaded as savedResidents):\n";
    $residents = DB::table('residents')
        ->where('family_id', $family->id)
        ->get();

    foreach ($residents as $r) {
        echo '  Member ID: ' . $r->id . "\n";
        echo '    nik: ' . ($r->nik ?? 'NULL') . "\n";
        echo '    nama_lengkap: ' . ($r->nama_lengkap ?? 'NULL') . "\n";
        echo '    family_relation_id: ' . ($r->family_relation_id ?? 'NULL') . "\n";
        echo '    jenis_kelamin: ' . ($r->jenis_kelamin ?? 'NULL') . "\n";
        echo '    tanggal_lahir: ' . ($r->tanggal_lahir ?? 'NULL') . "\n";
        echo '    tempat_lahir: ' . ($r->tempat_lahir ?? 'NULL') . "\n";
        echo '    golongan_darah: ' . ($r->golongan_darah ?? 'NULL') . "\n";

        // Calculate age
        if ($r->tanggal_lahir) {
            $dob = new DateTime($r->tanggal_lahir);
            $now = new DateTime();
            $age = $now->diff($dob)->y;
            echo '    umur (calculated): ' . $age . " tahun\n";
        }
        echo "\n";
    }
}

// Check answers for questions 21-23
echo "ANSWERS (for questions 21-23):\n";
$answers = DB::table('answers')
    ->where('response_id', 7)
    ->whereIn('question_id', [21, 22, 23])
    ->get();

if ($answers->isEmpty()) {
    echo "  NO ANSWERS YET (expected - will be filled by officer)\n\n";
} else {
    foreach ($answers as $ans) {
        echo '  Question ID: ' . $ans->question_id . "\n";
        echo '    answer_value: ' . ($ans->answer_value ?? 'NULL') . "\n";
        echo '    answer_text: ' . ($ans->answer_text ?? 'NULL') . "\n\n";
    }
}

echo "=== VERIFICATION COMPLETE ===\n\n";
echo "EXPECTED RESULT:\n";
echo "✅ Tabel anggota keluarga should show:\n";
echo "   - No: 1\n";
echo '   - NIK: ' . ($residents[0]->nik ?? 'NULL') . "\n";
echo '   - Nama: ' . ($residents[0]->nama_lengkap ?? 'NULL') . "\n";
echo '   - Status Keluarga: (from family_relation_id=' . ($residents[0]->family_relation_id ?? 'NULL') . ")\n";
echo '   - Jenis Kelamin: ' . ($residents[0]->jenis_kelamin ?? 'NULL') . "\n";
echo "   - Umur: (calculated from tanggal_lahir)\n\n";

echo "✅ Form fields should auto-fill:\n";
echo '   - Provinsi: (from province_id=' . ($family->province_id ?? 'NULL') . ")\n";
echo '   - Kabupaten: (from regency_id=' . ($family->regency_id ?? 'NULL') . ")\n";
echo '   - Kecamatan: (from district_id=' . ($family->district_id ?? 'NULL') . ")\n";
echo '   - Desa: (from village_id=' . ($family->village_id ?? 'NULL') . ")\n";
echo '   - RT: ' . ($family->rt ?? 'NULL') . "\n";
echo '   - RW: ' . ($family->rw ?? 'NULL') . "\n";
echo '   - No. Bangunan: ' . ($family->no_bangunan ?? 'NULL') . "\n";
echo '   - No. KK: ' . ($family->no_kk ?? 'NULL') . "\n";
echo '   - Alamat: ' . ($family->alamat ?? 'NULL') . "\n\n";

echo '✅ KK Image preview: ' . ($family->kk_image_path ? 'SHOWN' : 'NOT SHOWN') . "\n\n";

echo "⚠️ Catatan kuesioner: Currently empty (can be filled by officer)\n\n";
