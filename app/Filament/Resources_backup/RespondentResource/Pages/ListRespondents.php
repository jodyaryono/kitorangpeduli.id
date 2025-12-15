<?php

namespace App\Filament\Resources\RespondentResource\Pages;

use App\Filament\Resources\RespondentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRespondents extends ListRecords
{
    protected static string $resource = RespondentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
