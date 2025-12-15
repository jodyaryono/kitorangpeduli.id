<?php

namespace App\Filament\Resources\QuestionnaireResource\RelationManagers;

use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'Pertanyaan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('question_text')
                    ->label('Teks Pertanyaan')
                    ->required()
                    ->rows(2)
                    ->columnSpanFull(),

                Forms\Components\Select::make('question_type')
                    ->label('Tipe')
                    ->options(Question::TYPES)
                    ->required(),

                Forms\Components\Select::make('media_type')
                    ->label('Media')
                    ->options(Question::MEDIA_TYPES)
                    ->default('none'),

                Forms\Components\Toggle::make('is_required')
                    ->label('Wajib'),

                Forms\Components\TextInput::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_text')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#'),
                Tables\Columns\TextColumn::make('question_text')
                    ->label('Pertanyaan')
                    ->limit(40),
                Tables\Columns\TextColumn::make('question_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => Question::TYPES[$state] ?? $state),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('Wajib')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }
}
