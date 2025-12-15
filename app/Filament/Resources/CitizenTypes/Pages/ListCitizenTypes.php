<?php

namespace App\Filament\Resources\CitizenTypes\Pages;

use App\Filament\Resources\CitizenTypes\CitizenTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCitizenTypes extends ListRecords
{
    protected static string $resource = CitizenTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
