<?php

namespace App\Filament\Resources\Respondents\Pages;

use App\Filament\Resources\Respondents\RespondentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRespondent extends CreateRecord
{
    protected static string $resource = RespondentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user && ($user->isFieldOfficer() || $user->isOpdAdmin() || $user->isAdmin())) {
            $data['verification_status'] = 'verified';
            $data['verified_by'] = $user->id;
            $data['verified_at'] = now();
        }

        return $data;
    }
}
