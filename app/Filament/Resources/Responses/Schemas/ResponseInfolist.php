<?php

namespace App\Filament\Resources\Responses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ResponseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('questionnaire.title')
                    ->label('Questionnaire'),
                TextEntry::make('respondent.id')
                    ->label('Respondent'),
                TextEntry::make('status'),
                TextEntry::make('lastQuestion.id')
                    ->label('Last question')
                    ->placeholder('-'),
                TextEntry::make('progress_percentage')
                    ->numeric(),
                TextEntry::make('latitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('longitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('device_info')
                    ->placeholder('-'),
                TextEntry::make('ip_address')
                    ->placeholder('-'),
                TextEntry::make('started_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_valid')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('validation_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
