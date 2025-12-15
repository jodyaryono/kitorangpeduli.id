<?php

namespace Database\Seeders;

use App\Models\Opd;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TempOfficerSeeder extends Seeder
{
    public function run(): void
    {
        $opd = Opd::select('id', 'name')->first();

        if (!$opd) {
            $this->command->warn('No OPD found. Skipping officer creation.');
            return;
        }

        $email = 'officer@opd.local';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Field Officer Test',
                'password' => Hash::make('Password123!'),
                'role' => 'field_officer',
                'opd_id' => $opd->id,
                'is_active' => true,
            ]
        );

        $message = 'Officer user ready: ' . $user->email . ' | password: Password123! | OPD: ' . $opd->name;
        if ($this->command) {
            $this->command->info($message);
        } else {
            echo $message . PHP_EOL;
        }
    }
}
