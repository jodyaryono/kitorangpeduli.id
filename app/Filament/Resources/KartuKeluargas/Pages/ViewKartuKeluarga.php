<?php

namespace App\Filament\Resources\KartuKeluargas\Pages;

use App\Filament\Resources\KartuKeluargas\KartuKeluargaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKartuKeluarga extends ViewRecord
{
    protected static string $resource = KartuKeluargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
