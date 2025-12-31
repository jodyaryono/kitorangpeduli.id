<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

echo "\n=== DEBUG AUTOSAVE ROUTE ===\n\n";

// Check route
$route = Route::getRoutes()->getByName('questionnaire.autosave');
if ($route) {
    echo "✅ Route exists: questionnaire.autosave\n";
    echo '   URI: ' . $route->uri() . "\n";
    echo '   Method: ' . implode('|', $route->methods()) . "\n";
    echo '   Action: ' . $route->getActionName() . "\n";
    echo '   Middleware: ' . implode(', ', $route->middleware()) . "\n\n";
} else {
    echo "❌ Route not found\n\n";
}

// Check response and session
$response = DB::table('responses')->where('id', 7)->first();
echo "RESPONSE:\n";
echo '  id: ' . $response->id . "\n";
echo '  resident_id: ' . ($response->resident_id ?? 'NULL') . "\n";
echo '  status: ' . $response->status . "\n";
echo '  officer_notes: ' . ($response->officer_notes ?? 'NULL') . "\n\n";

// Check if there's a session issue
echo "AUTHENTICATION CHECK:\n";
echo "1. Route autosave does NOT require 'auth' middleware\n";
echo "2. Controller checks session('officer_assisted') or session('resident')\n";
echo "3. If both are null, returns 401 Unauthorized\n\n";

echo "POSSIBLE ISSUES:\n";
echo "1. ❌ 404 Not Found - Laravel tidak bisa resolve route\n";
echo "   - Check if route cache needs clearing: php artisan route:clear\n";
echo "   - Check if there's conflicting Filament routes\n\n";

echo "2. ⚠️ Session not persisted between requests\n";
echo "   - officer_assisted session might be lost\n";
echo "   - Check if session driver is working (file/database/redis)\n\n";

echo "3. ⚠️ CSRF token mismatch\n";
echo "   - Check if X-CSRF-TOKEN header is correct\n";
echo "   - Blade should render {{ csrf_token() }} correctly\n\n";

echo "SOLUTION: Try adding middleware('auth') to autosave route\n";
echo "  Route::post('/questionnaire/{id}/autosave', ...)->middleware('auth');\n\n";

echo "OR: Check if accessing via Filament panel requires different prefix\n";
