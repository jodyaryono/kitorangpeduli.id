<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Pusat Kota Jayapura
$centerLat = -2.5333;
$centerLng = 140.7167;

$respondents = App\Models\Respondent::all();

echo "Verifikasi GPS Coordinates Respondents\n";
echo "========================================\n";
echo "Pusat Jayapura: {$centerLat}, {$centerLng}\n";
echo 'Total Respondents: ' . $respondents->count() . "\n\n";

// Calculate distance using Haversine formula
function haversineDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371;  // km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2)
        + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;

    return $distance;
}

$distances = [];
foreach ($respondents as $r) {
    $distance = haversineDistance($centerLat, $centerLng, $r->latitude, $r->longitude);
    $distances[] = $distance;
}

sort($distances);

echo "Jarak dari Pusat Kota:\n";
echo '  Min: ' . number_format($distances[0], 2) . " km\n";
echo '  Max: ' . number_format($distances[count($distances) - 1], 2) . " km\n";
echo '  Avg: ' . number_format(array_sum($distances) / count($distances), 2) . " km\n";
echo '  Median: ' . number_format($distances[floor(count($distances) / 2)], 2) . " km\n\n";

// Count by radius
$radius5 = count(array_filter($distances, fn($d) => $d <= 5));
$radius10 = count(array_filter($distances, fn($d) => $d <= 10));
$radius15 = count(array_filter($distances, fn($d) => $d <= 15));

echo "Distribusi Radius:\n";
echo "  <= 5 km:  {$radius5} responden (" . round($radius5 / count($distances) * 100, 1) . "%)\n";
echo "  <= 10 km: {$radius10} responden (" . round($radius10 / count($distances) * 100, 1) . "%)\n";
echo "  <= 15 km: {$radius15} responden (" . round($radius15 / count($distances) * 100, 1) . "%)\n\n";

// Sample coordinates
echo "Sample 5 Koordinat:\n";
foreach ($respondents->take(5) as $r) {
    $distance = haversineDistance($centerLat, $centerLng, $r->latitude, $r->longitude);
    echo "  {$r->nama_lengkap}: ({$r->latitude}, {$r->longitude}) - "
        . number_format($distance, 2) . " km dari pusat\n";
}
