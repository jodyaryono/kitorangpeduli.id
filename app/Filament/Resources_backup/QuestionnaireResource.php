<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionnaireResource\Pages;
use App\Filament\Resources\QuestionnaireResource\RelationManagers;
use App\Models\Questionnaire;
use App\Models\Opd;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionnaireResource extends Resource
{
    protected static ?string $model = Questionnaire::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string | \UnitEnum | null $navigationGroup = 'Kuesioner';

    protected static ?string $modelLabel = 'Kuesioner';

    protected static ?string $pluralModelLabel = 'Kuesioner';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kuesioner')
                    ->schema([
                        Forms\Components\Select::make('opd_id')
                            ->label('OPD')
                            ->options(Opd::active()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Kuesioner')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                    ])->columns(1),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image_path')
                            ->label('Cover Image')
                            ->image()
                            ->directory('questionnaire-covers')
                            ->maxSize(5120),
                        Forms\Components\FileUpload::make('cover_video_path')
                            ->label('Cover Video')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->directory('questionnaire-videos')
                            ->maxSize(51200),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai'),
                        Forms\Components\TextInput::make('max_responses')
                            ->label('Maks Responden')
                            ->numeric()
                            ->helperText('Kosongkan untuk tidak membatasi'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Toggle::make('requires_location')
                            ->label('Wajib Lokasi GPS')
                            ->default(true),
                        Forms\Components\Toggle::make('requires_verified_respondent')
                            ->label('Wajib Responden Terverifikasi')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('opd.name')
                    ->label('OPD')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Pertanyaan')
                    ->counts('questions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('responses_count')
                    ->label('Responden')
                    ->counts('responses')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('opd_id')
                    ->label('OPD')
                    ->options(Opd::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\Action::make('questions')
                    ->label('Pertanyaan')
                    ->icon('heroicon-o-question-mark-circle')
                    ->url(fn (Questionnaire $record): string => route('filament.admin.resources.questionnaires.questions', $record)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionnaires::route('/'),
            'create' => Pages\CreateQuestionnaire::route('/create'),
            'edit' => Pages\EditQuestionnaire::route('/{record}/edit'),
            'questions' => Pages\ManageQuestions::route('/{record}/questions'),
        ];
    }
}
