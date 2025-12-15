<?php

namespace App\Filament\Resources\Responses\Pages;

use App\Filament\Resources\Responses\ResponseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewResponse extends ViewRecord
{
    protected static string $resource = ResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
