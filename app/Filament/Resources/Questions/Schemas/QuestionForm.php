<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('questionnaire_id')
                    ->relationship('questionnaire', 'title')
                    ->required(),
                Textarea::make('question_text')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('question_type')
                    ->required()
                    ->default('text'),
                TextInput::make('media_type')
                    ->required()
                    ->default('none'),
                TextInput::make('media_path'),
                Toggle::make('is_required')
                    ->required(),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('settings'),
                Toggle::make('is_repeatable')
                    ->required(),
                TextInput::make('applies_to')
                    ->required()
                    ->default('individual'),
            ]);
    }
}
