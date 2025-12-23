<?php

/**
 * Script to add Latitude & Longitude with GPS/Map Picker to Section I
 * Run: php add_gps_location_fields.php
 */
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Question;

echo "🔄 Adding GPS Location Fields to Section I...\n\n";

// Find Section I
$sectionI = Question::where('question_text', 'I. PENGENALAN TEMPAT')
    ->where('questionnaire_id', 8)
    ->where('is_section', true)
    ->first();

if (!$sectionI) {
    echo "❌ Section I not found\n";
    exit(1);
}

echo "✅ Found Section I (ID: {$sectionI->id})\n\n";

// Get last order number in Section I
$lastOrder = Question::where('parent_section_id', $sectionI->id)
    ->where('questionnaire_id', 8)
    ->max('order') ?? 9;

echo "📍 Last order in Section I: {$lastOrder}\n\n";

// Check if location question already exists
$existingLocation = Question::where('parent_section_id', $sectionI->id)
    ->where('question_type', 'location')
    ->where('questionnaire_id', 8)
    ->first();

if ($existingLocation) {
    echo "ℹ️  Location field already exists (ID: {$existingLocation->id})\n";
} else {
    echo "➕ Adding Location (GPS/Map Picker) field...\n";

    $locationQuestion = Question::create([
        'questionnaire_id' => 8,
        'parent_section_id' => $sectionI->id,
        'question_text' => 'Lokasi GPS (Latitude & Longitude)',
        'question_type' => 'location',
        'order' => $lastOrder + 1,
        'is_required' => true,
        'applies_to' => 'family',
        'settings' => json_encode([
            'auto_detect' => true,
            'manual_pick' => true,
            'show_map' => true,
            'cascade_from_wilayah' => true
        ])
    ]);

    echo "   ✅ Location field created (ID: {$locationQuestion->id})\n";
}

echo "\n✅ GPS Location fields added successfully!\n";
echo "\n📝 Features:\n";
echo "   - Auto-detect GPS dari device\n";
echo "   - Manual pick dari peta\n";
echo "   - Initial marker dari wilayah cascade\n";
echo "   - Map dengan Leaflet.js\n";
