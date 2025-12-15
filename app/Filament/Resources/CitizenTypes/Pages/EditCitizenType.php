<?php

namespace App\Filament\Resources\CitizenTypes\Pages;

use App\Filament\Resources\CitizenTypes\CitizenTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCitizenType extends EditRecord
{
    protected static string $resource = CitizenTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
