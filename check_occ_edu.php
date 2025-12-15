<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking occupation and education data:\n\n";

// Check occupation data
$occupations = App\Models\Occupation::all();
echo 'Total Occupations: ' . $occupations->count() . "\n";
if ($occupations->count() > 0) {
    echo "Sample occupations:\n";
    foreach ($occupations->take(10) as $occ) {
        echo "  - ID: {$occ->id}, Name: {$occ->name}\n";
    }
}

echo "\n";

// Check education data
$educations = App\Models\Education::all();
echo 'Total Educations: ' . $educations->count() . "\n";
if ($educations->count() > 0) {
    echo "Sample educations:\n";
    foreach ($educations->take(10) as $edu) {
        echo "  - ID: {$edu->id}, Name: {$edu->name}\n";
    }
}

echo "\n";

// Check respondents with occupation_id
$respondentsWithOccupation = App\Models\Respondent::whereNotNull('occupation_id')->count();
echo "Respondents with occupation_id: {$respondentsWithOccupation}\n";

// Check respondents with education_id
$respondentsWithEducation = App\Models\Respondent::whereNotNull('education_id')->count();
echo "Respondents with education_id: {$respondentsWithEducation}\n";

echo "\n";

// Sample respondents with occupation and education
echo "Sample respondents with occupation and education:\n";
$sampleRespondents = App\Models\Respondent::with(['occupation', 'education'])
    ->whereNotNull('occupation_id')
    ->orWhereNotNull('education_id')
    ->limit(5)
    ->get();

foreach ($sampleRespondents as $respondent) {
    echo "- {$respondent->nama_lengkap}: ";
    echo 'Occupation: ' . ($respondent->occupation?->name ?? 'N/A') . ', ';
    echo 'Education: ' . ($respondent->education?->name ?? 'N/A') . "\n";
}
