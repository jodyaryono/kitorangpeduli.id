<?php

namespace App\Filament\Resources\Respondents\Pages;

use App\Filament\Resources\Respondents\RespondentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRespondents extends ListRecords
{
    protected static string $resource = RespondentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
