<?php

namespace App\Filament\Resources\CitizenTypeResource\Pages;

use App\Filament\Resources\CitizenTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCitizenType extends EditRecord
{
    protected static string $resource = CitizenTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
