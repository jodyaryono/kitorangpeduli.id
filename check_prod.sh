#!/bin/bash
cd /var/www/kitorangpeduli.id
/usr/bin/php8.2 artisan tinker --execute="echo 'Respondents with occupation_id: ' . App\\Models\\Respondent::whereNotNull('occupation_id')->count() . PHP_EOL; echo 'Respondents with education_id: ' . App\\Models\\Respondent::whereNotNull('education_id')->count() . PHP_EOL; echo 'Total respondents: ' . App\\Models\\Respondent::count() . PHP_EOL;"
