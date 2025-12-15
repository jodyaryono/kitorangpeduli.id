<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Questionnaire;
use App\Models\Respondent;
use App\Models\Response;

echo "Checking data after migrate:fresh...\n\n";
echo 'Questionnaires: ' . Questionnaire::count() . "\n";
echo 'Respondents: ' . Respondent::count() . "\n";
echo 'Responses: ' . Response::count() . "\n";
