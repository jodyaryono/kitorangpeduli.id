<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Answer;
use App\Models\Family;
use App\Models\Response;

echo "Testing syncFamilyData for Response ID 2\n";
echo str_repeat('=', 80) . "\n\n";

$response = Response::find(2);

if (!$response) {
    echo "Response not found!\n";
    exit;
}

echo "Response ID: {$response->id}\n";
echo "Resident ID: {$response->resident_id}\n\n";

// Question IDs for family data
$familyQuestionMap = [
    214 => 'province_id',
    215 => 'regency_id',
    216 => 'district_id',
    217 => 'village_id',
    220 => 'rt',
    219 => 'rw',
    225 => 'alamat',
    269 => 'kepala_keluarga',
    223 => 'no_kk',
    266 => 'kk_image_path',
];

// Get all answers
$answers = Answer::where('response_id', $response->id)
    ->whereIn('question_id', array_keys($familyQuestionMap))
    ->get()
    ->keyBy('question_id');

echo "Found {$answers->count()} KK-related answers:\n";
foreach ($answers as $qId => $answer) {
    $column = $familyQuestionMap[$qId];
    $value = $answer->media_path ?? $answer->answer_text ?? $answer->answer_numeric;
    echo "  Q{$qId} ({$column}): {$value}\n";
}
echo "\n";

// Prepare family data (convert nama to ID for wilayah)
$familyData = [];

// Province
if ($provinceAnswer = $answers->get(214)) {
    $value = $provinceAnswer->answer_text;
    // Check if it's already an ID (numeric)
    if (is_numeric($value)) {
        $familyData['province_id'] = $value;
    } else {
        // Convert name to ID
        $province = App\Models\Province::where('name', 'LIKE', '%' . $value . '%')->first();
        if ($province) {
            $familyData['province_id'] = $province->id;
            echo "Converted province '{$value}' to ID: {$province->id}\n";
        }
    }
}

// Regency
if ($regencyAnswer = $answers->get(215)) {
    $value = $regencyAnswer->answer_text;
    if (is_numeric($value)) {
        $familyData['regency_id'] = $value;
    } else {
        $regency = App\Models\Regency::where('name', 'LIKE', '%' . $value . '%')
            ->when(isset($familyData['province_id']), fn($q) => $q->where('province_id', $familyData['province_id']))
            ->first();
        if ($regency) {
            $familyData['regency_id'] = $regency->id;
            echo "Converted regency '{$value}' to ID: {$regency->id}\n";
        }
    }
}

// District
if ($districtAnswer = $answers->get(216)) {
    $value = $districtAnswer->answer_text;
    if (is_numeric($value)) {
        $familyData['district_id'] = $value;
    } else {
        $district = App\Models\District::where('name', 'LIKE', '%' . $value . '%')
            ->when(isset($familyData['regency_id']), fn($q) => $q->where('regency_id', $familyData['regency_id']))
            ->first();
        if ($district) {
            $familyData['district_id'] = $district->id;
            echo "Converted district '{$value}' to ID: {$district->id}\n";
        }
    }
}

// Village
if ($villageAnswer = $answers->get(217)) {
    $value = $villageAnswer->answer_text;
    if (is_numeric($value)) {
        $familyData['village_id'] = $value;
    } else {
        $village = App\Models\Village::where('name', 'LIKE', '%' . $value . '%')
            ->when(isset($familyData['district_id']), fn($q) => $q->where('district_id', $familyData['district_id']))
            ->first();
        if ($village) {
            $familyData['village_id'] = $village->id;
            echo "Converted village '{$value}' to ID: {$village->id}\n";
        }
    }
}

// RT, RW, Alamat
foreach ([220 => 'rt', 219 => 'rw', 225 => 'alamat', 269 => 'kepala_keluarga', 223 => 'no_kk'] as $qId => $column) {
    $answer = $answers->get($qId);
    if ($answer && $answer->answer_text && $answer->answer_text != '-') {
        $familyData[$column] = $answer->answer_text;
    }
}

// KK Image
if ($kkImageAnswer = $answers->get(266)) {
    if ($kkImageAnswer->media_path) {
        $familyData['kk_image_path'] = $kkImageAnswer->media_path;
    }
}

echo "\nFamily data to sync:\n";
print_r($familyData);
echo "\n";

if (empty($familyData)) {
    echo "No valid family data!\n";
    exit;
}

// Find existing family (Family ID 1)
$family = Family::find(1);

if ($family) {
    echo "Updating existing Family ID: {$family->id}\n";
    $family->update($familyData);
    echo "✅ Family updated successfully!\n\n";

    echo "Updated family data:\n";
    echo '  No KK: ' . ($family->no_kk ?? 'NULL') . "\n";
    echo '  Kepala Keluarga: ' . ($family->kepala_keluarga ?? 'NULL') . "\n";
    echo "  Alamat: {$family->alamat}\n";
    echo "  RT/RW: {$family->rt}/{$family->rw}\n";
    echo '  KK Image: ' . ($family->kk_image_path ?? 'NULL') . "\n";
    echo "  Province: {$family->province_id}\n";
    echo "  Regency: {$family->regency_id}\n";
    echo "  District: {$family->district_id}\n";
    echo "  Village: {$family->village_id}\n";
} else {
    echo "Creating new family...\n";
    $family = Family::create($familyData);
    echo "✅ Family created with ID: {$family->id}\n";
}

echo "\nDone!\n";
