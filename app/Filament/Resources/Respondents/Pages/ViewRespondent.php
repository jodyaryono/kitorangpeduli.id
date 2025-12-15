<?php

namespace App\Filament\Resources\Respondents\Pages;

use App\Filament\Resources\Respondents\RespondentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewRespondent extends ViewRecord
{
    protected static string $resource = RespondentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getFooter(): ?View
    {
        return view('filament.resources.respondents.partials.map-section', [
            'record' => $this->record,
        ]);
    }
}
