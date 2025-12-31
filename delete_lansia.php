<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cat = \App\Models\HealthQuestionCategory::where('code', 'lansia')->first();
if ($cat) {
    $cat->delete();
    echo "✅ Deleted lansia category\n";
} else {
    echo "❌ Category not found\n";
}
