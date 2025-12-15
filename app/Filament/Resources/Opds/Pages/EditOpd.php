<?php

namespace App\Filament\Resources\Opds\Pages;

use App\Filament\Resources\Opds\OpdResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOpd extends EditRecord
{
    protected static string $resource = OpdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
