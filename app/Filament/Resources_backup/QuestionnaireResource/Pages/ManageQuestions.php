<?php

namespace App\Filament\Resources\QuestionnaireResource\Pages;

use App\Filament\Resources\QuestionnaireResource;
use App\Models\Question;
use App\Models\Questionnaire;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ManageQuestions extends ManageRelatedRecords
{
    protected static string $resource = QuestionnaireResource::class;

    protected static string $relationship = 'questions';

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $title = 'Kelola Pertanyaan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('question_text')
                    ->label('Teks Pertanyaan')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Select::make('question_type')
                    ->label('Tipe Pertanyaan')
                    ->options(Question::TYPES)
                    ->required()
                    ->reactive(),

                Forms\Components\Select::make('media_type')
                    ->label('Tipe Media')
                    ->options(Question::MEDIA_TYPES)
                    ->default('none')
                    ->reactive(),

                Forms\Components\FileUpload::make('media_path')
                    ->label('Media')
                    ->directory('question-media')
                    ->maxSize(51200)
                    ->visible(fn (callable $get) => $get('media_type') !== 'none'),

                Forms\Components\Toggle::make('is_required')
                    ->label('Wajib Dijawab')
                    ->default(false),

                Forms\Components\TextInput::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),

                Forms\Components\KeyValue::make('settings')
                    ->label('Pengaturan Tambahan')
                    ->helperText('Contoh: min_scale=1, max_scale=5')
                    ->columnSpanFull(),

                Forms\Components\Section::make('Opsi Jawaban')
                    ->schema([
                        Forms\Components\Repeater::make('options')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('option_text')
                                    ->label('Teks Opsi')
                                    ->required(),
                                Forms\Components\Select::make('media_type')
                                    ->label('Media')
                                    ->options(Question::MEDIA_TYPES)
                                    ->default('none'),
                                Forms\Components\FileUpload::make('media_path')
                                    ->label('File Media')
                                    ->directory('option-media'),
                                Forms\Components\TextInput::make('order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(4)
                            ->orderColumn('order')
                            ->reorderable()
                            ->collapsible(),
                    ])
                    ->visible(fn (callable $get) => in_array($get('question_type'), ['single_choice', 'multiple_choice', 'dropdown']))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_text')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('question_text')
                    ->label('Pertanyaan')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('question_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => Question::TYPES[$state] ?? $state),
                Tables\Columns\TextColumn::make('media_type')
                    ->label('Media')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'success',
                        'video' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('Wajib')
                    ->boolean(),
                Tables\Columns\TextColumn::make('options_count')
                    ->label('Opsi')
                    ->counts('options'),
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
