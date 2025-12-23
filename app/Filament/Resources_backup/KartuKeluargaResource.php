<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KartuKeluargaResource\Pages;
use App\Models\Family;
use App\Models\Village;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KartuKeluargaResource extends Resource
{
    protected static ?string $model = Family::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static string | \UnitEnum | null $navigationGroup = 'Data Penduduk';

    protected static ?string $modelLabel = 'Kartu Keluarga';

    protected static ?string $pluralModelLabel = 'Kartu Keluarga';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Kartu Keluarga')
                    ->schema([
                        Forms\Components\TextInput::make('no_kk')
                            ->label('Nomor KK')
                            ->required()
                            ->maxLength(16)
                            ->minLength(16)
                            ->numeric()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('kepala_keluarga')
                            ->label('Nama Kepala Keluarga')
                            ->required()
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Alamat')
                    ->schema([
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('rt')
                            ->label('RT')
                            ->maxLength(5),
                        Forms\Components\TextInput::make('rw')
                            ->label('RW')
                            ->maxLength(5),
                        Forms\Components\Select::make('province_id')
                            ->label('Provinsi')
                            ->relationship('province', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('regency_id', null)),
                        Forms\Components\Select::make('regency_id')
                            ->label('Kabupaten/Kota')
                            ->relationship('regency', 'name', fn ($query, callable $get) => $query->where('province_id', $get('province_id')))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('district_id', null)),
                        Forms\Components\Select::make('district_id')
                            ->label('Kecamatan')
                            ->relationship('district', 'name', fn ($query, callable $get) => $query->where('regency_id', $get('regency_id')))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('village_id', null)),
                        Forms\Components\Select::make('village_id')
                            ->label('Kelurahan/Desa')
                            ->relationship('village', 'name', fn ($query, callable $get) => $query->where('district_id', $get('district_id')))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Dokumen & Verifikasi')
                    ->schema([
                        Forms\Components\FileUpload::make('kk_image_path')
                            ->label('Foto KK')
                            ->image()
                            ->directory('kk-images')
                            ->maxSize(5120)
                            ->required(),
                        Forms\Components\Select::make('verification_status')
                            ->label('Status Verifikasi')
                            ->options(Family::VERIFICATION_STATUSES)
                            ->default('pending')
                            ->required(),
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Catatan Verifikasi')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_kk')
                    ->label('No. KK')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('kepala_keluarga')
                    ->label('Kepala Keluarga')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('respondents_count')
                    ->label('Anggota')
                    ->counts('respondents')
                    ->sortable(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Kelurahan')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('Kecamatan')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('regency.name')
                    ->label('Kab/Kota')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('verification_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => Family::VERIFICATION_STATUSES[$state] ?? $state),
                Tables\Columns\ImageColumn::make('kk_image_path')
                    ->label('KK')
                    ->circular(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('verification_status')
                    ->label('Status')
                    ->options(Family::VERIFICATION_STATUSES),
                Tables\Filters\SelectFilter::make('province_id')
                    ->label('Provinsi')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (KartuKeluarga $record): bool => $record->verification_status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn (KartuKeluarga $record) => $record->update([
                        'verification_status' => 'verified',
                        'verified_at' => now(),
                        'verified_by' => auth()->id(),
                    ])),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (KartuKeluarga $record): bool => $record->verification_status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(fn (KartuKeluarga $record, array $data) => $record->update([
                        'verification_status' => 'rejected',
                        'verification_notes' => $data['verification_notes'],
                        'verified_at' => now(),
                        'verified_by' => auth()->id(),
                    ])),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKartuKeluargas::route('/'),
            'create' => Pages\CreateFamily::route('/create'),
            'view' => Pages\ViewFamily::route('/{record}'),
            'edit' => Pages\EditFamily::route('/{record}/edit'),
        ];
    }
}
