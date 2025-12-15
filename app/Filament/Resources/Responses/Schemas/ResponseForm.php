<?php

namespace App\Filament\Resources\Responses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ResponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('questionnaire_id')
                    ->relationship('questionnaire', 'title')
                    ->required(),
                Select::make('respondent_id')
                    ->relationship('respondent', 'id')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('in_progress'),
                Select::make('last_question_id')
                    ->relationship('lastQuestion', 'id'),
                TextInput::make('progress_percentage')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                TextInput::make('device_info'),
                TextInput::make('ip_address'),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
                Toggle::make('is_valid'),
                Textarea::make('validation_notes')
                    ->columnSpanFull(),
            ]);
    }
}
