<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Answer;
use App\Models\District;
use App\Models\Family;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Response;
use App\Models\Village;

echo "Converting old KK data (names) to IDs and syncing to families\n";
echo str_repeat('=', 80) . "\n\n";

$response = Response::find(2);

if (!$response) {
    echo "Response not found!\n";
    exit;
}

echo "Processing Response ID: {$response->id}\n\n";

// Get answers
$answers = Answer::where('response_id', $response->id)
    ->whereIn('question_id', [214, 215, 216, 217, 219, 220, 223, 225, 266, 269])
    ->get()
    ->keyBy('question_id');

// Convert names to IDs
$provinceId = null;
$regencyId = null;
$districtId = null;
$villageId = null;

if ($provinceAnswer = $answers->get(214)) {
    $province = Province::where('name', 'LIKE', '%' . $provinceAnswer->answer_text . '%')->first();
    if ($province) {
        $provinceId = $province->id;
        echo "Found Province: {$province->name} (ID: {$province->id})\n";
    }
}

if ($regencyAnswer = $answers->get(215)) {
    $regency = Regency::where('name', 'LIKE', '%' . $regencyAnswer->answer_text . '%')
        ->when($provinceId, fn($q) => $q->where('province_id', $provinceId))
        ->first();
    if ($regency) {
        $regencyId = $regency->id;
        echo "Found Regency: {$regency->name} (ID: {$regency->id})\n";
    }
}

if ($districtAnswer = $answers->get(216)) {
    $district = District::where('name', 'LIKE', '%' . $districtAnswer->answer_text . '%')
        ->when($regencyId, fn($q) => $q->where('regency_id', $regencyId))
        ->first();
    if ($district) {
        $districtId = $district->id;
        echo "Found District: {$district->name} (ID: {$district->id})\n";
    }
}

if ($villageAnswer = $answers->get(217)) {
    $village = Village::where('name', 'LIKE', '%' . $villageAnswer->answer_text . '%')
        ->when($districtId, fn($q) => $q->where('district_id', $districtId))
        ->first();
    if ($village) {
        $villageId = $village->id;
        echo "Found Village: {$village->name} (ID: {$village->id})\n";
    }
}

echo "\n";

// Prepare family data
$familyData = [];

if ($provinceId)
    $familyData['province_id'] = $provinceId;
if ($regencyId)
    $familyData['regency_id'] = $regencyId;
if ($districtId)
    $familyData['district_id'] = $districtId;
if ($villageId)
    $familyData['village_id'] = $villageId;

if ($rtAnswer = $answers->get(220)) {
    $familyData['rt'] = $rtAnswer->answer_text;
}

if ($rwAnswer = $answers->get(219)) {
    $familyData['rw'] = $rwAnswer->answer_text;
}

if ($alamatAnswer = $answers->get(225)) {
    $familyData['alamat'] = $alamatAnswer->answer_text;
}

if ($kepalaAnswer = $answers->get(269)) {
    if ($kepalaAnswer->answer_text && $kepalaAnswer->answer_text != '-') {
        $familyData['kepala_keluarga'] = $kepalaAnswer->answer_text;
    }
}

if ($noKkAnswer = $answers->get(223)) {
    if ($noKkAnswer->answer_text && $noKkAnswer->answer_text != '-') {
        $familyData['no_kk'] = $noKkAnswer->answer_text;
    }
}

if ($kkImageAnswer = $answers->get(266)) {
    if ($kkImageAnswer->media_path) {
        $familyData['kk_image_path'] = $kkImageAnswer->media_path;
    }
}

echo "Family data to insert:\n";
print_r($familyData);
echo "\n";

if (empty($familyData)) {
    echo "No valid family data!\n";
    exit;
}

try {
    $family = Family::create($familyData);
    echo "✅ Successfully created Family ID: {$family->id}\n";
    echo '   No KK: ' . ($family->no_kk ?? 'NULL') . "\n";
    echo '   Kepala Keluarga: ' . ($family->kepala_keluarga ?? 'NULL') . "\n";
    echo "   Alamat: {$family->alamat}\n";
    echo "   RT/RW: {$family->rt}/{$family->rw}\n";
    echo "   Province: {$family->province_id}\n";
    echo "   Regency: {$family->regency_id}\n";
    echo "   District: {$family->district_id}\n";
    echo "   Village: {$family->village_id}\n";
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

echo "\nDone!\n";
