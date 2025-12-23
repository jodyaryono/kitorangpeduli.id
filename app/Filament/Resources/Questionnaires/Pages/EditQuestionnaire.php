<?php

namespace App\Filament\Resources\Questionnaires\Pages;

use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditQuestionnaire extends EditRecord
{
    protected static string $resource = QuestionnaireResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Increase memory limit for loading large questionnaires
        ini_set('memory_limit', '512M');

        return $data;
    }

    protected function resolveRecord(string|int $key): Model
    {
        // Eager load questions with their options to optimize query
        return static::getModel()::with(['questions.options'])->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview Kuesioner')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn ($record) => route('questionnaire.preview', $record))
                ->openUrlInNewTab(),
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->isAdmin() || auth()->user()?->isOpdAdmin()),
        ];
    }
}
