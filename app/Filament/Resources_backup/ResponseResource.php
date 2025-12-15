<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResponseResource\Pages;
use App\Models\Response;
use App\Models\Questionnaire;
use App\Models\Respondent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ResponseResource extends Resource
{
    protected static ?string $model = Response::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-check';

    protected static string | \UnitEnum | null $navigationGroup = 'Kuesioner';

    protected static ?string $modelLabel = 'Jawaban Survey';

    protected static ?string $pluralModelLabel = 'Jawaban Survey';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Survey')
                    ->schema([
                        Forms\Components\Select::make('questionnaire_id')
                            ->label('Kuesioner')
                            ->relationship('questionnaire', 'title')
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('respondent_id')
                            ->label('Responden')
                            ->relationship('respondent', 'nama_lengkap')
                            ->required()
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(Response::STATUSES)
                            ->required(),
                        Forms\Components\Toggle::make('is_valid')
                            ->label('Valid'),
                        Forms\Components\Textarea::make('validation_notes')
                            ->label('Catatan Validasi')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->disabled(),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('questionnaire.title')
                    ->label('Kuesioner')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('questionnaire.opd.name')
                    ->label('OPD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('respondent.nama_lengkap')
                    ->label('Responden')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('respondent.citizenType.name')
                    ->label('Jenis Warga')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('respondent.village.name')
                    ->label('Kelurahan')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('answers_count')
                    ->label('Jawaban')
                    ->counts('answers')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'in_progress',
                        'success' => 'completed',
                        'primary' => 'submitted',
                    ])
                    ->formatStateUsing(fn (string $state): string => Response::STATUSES[$state] ?? $state),
                Tables\Columns\IconColumn::make('is_valid')
                    ->label('Valid')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('questionnaire_id')
                    ->label('Kuesioner')
                    ->relationship('questionnaire', 'title')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Response::STATUSES),
                Tables\Filters\TernaryFilter::make('is_valid')
                    ->label('Valid'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_answers')
                    ->label('Lihat Jawaban')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Response $record): string => route('filament.admin.resources.responses.view', $record)),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResponses::route('/'),
            'edit' => Pages\EditResponse::route('/{record}/edit'),
            'view' => Pages\ViewResponse::route('/{record}'),
        ];
    }
}
