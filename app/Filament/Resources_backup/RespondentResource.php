<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RespondentResource\Pages;
use App\Models\Respondent;
use App\Models\Family;
use App\Models\CitizenType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RespondentResource extends Resource
{
    protected static ?string $model = Respondent::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Data Penduduk';

    protected static ?string $modelLabel = 'Responden';

    protected static ?string $pluralModelLabel = 'Responden';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data KTP')
                    ->schema([
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->required()
                            ->maxLength(16)
                            ->minLength(16)
                            ->numeric()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->maxLength(50),
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir'),
                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options(Respondent::GENDERS)
                            ->required(),
                        Forms\Components\Select::make('golongan_darah')
                            ->label('Golongan Darah')
                            ->options(Respondent::BLOOD_TYPES),
                        Forms\Components\Select::make('agama')
                            ->label('Agama')
                            ->options(Respondent::RELIGIONS),
                        Forms\Components\Select::make('status_perkawinan')
                            ->label('Status Perkawinan')
                            ->options(Respondent::MARITAL_STATUSES),
                        Forms\Components\TextInput::make('pekerjaan')
                            ->label('Pekerjaan')
                            ->maxLength(50),
                        Forms\Components\Select::make('pendidikan')
                            ->label('Pendidikan')
                            ->options(Respondent::EDUCATIONS),
                    ])->columns(2),

                Forms\Components\Section::make('Kartu Keluarga & Jenis Warga')
                    ->schema([
                        Forms\Components\Select::make('families_id')
                            ->label('Kartu Keluarga')
                            ->options(fn () => Family::all()->pluck('kepala_keluarga', 'id')->mapWithKeys(fn ($name, $id) => [$id => Family::find($id)->no_kk . ' - ' . $name]))
                            ->searchable()
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('status_hubungan')
                            ->label('Status Hubungan dalam KK')
                            ->options(Respondent::FAMILY_RELATIONS)
                            ->required(),
                        Forms\Components\Select::make('citizen_type_id')
                            ->label('Jenis Warga')
                            ->relationship('citizenType', 'name')
                            ->required()
                            ->preload(),
                        Forms\Components\Select::make('kewarganegaraan')
                            ->label('Kewarganegaraan')
                            ->options(Respondent::NATIONALITIES)
                            ->default('WNI'),
                    ])->columns(2),

                Forms\Components\Section::make('Alamat Domisili')
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

                Forms\Components\Section::make('Kontak & Lokasi GPS')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('No. WhatsApp')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.0000001),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.0000001),
                    ])->columns(2),

                Forms\Components\Section::make('Dokumen & Verifikasi')
                    ->schema([
                        Forms\Components\FileUpload::make('ktp_image_path')
                            ->label('Foto KTP')
                            ->image()
                            ->directory('ktp-images')
                            ->maxSize(5120)
                            ->required(),
                        Forms\Components\FileUpload::make('selfie_ktp_path')
                            ->label('Foto Selfie dengan KTP')
                            ->image()
                            ->directory('selfie-ktp')
                            ->maxSize(5120),
                        Forms\Components\Select::make('verification_status')
                            ->label('Status Verifikasi')
                            ->options(Respondent::VERIFICATION_STATUSES)
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
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('citizenType.name')
                    ->label('Jenis Warga')
                    ->badge()
                    ->color(fn (Respondent $record): string => match ($record->citizenType?->code) {
                        'OAP' => 'success',
                        'PORTNUMBAY' => 'info',
                        'WNA' => 'warning',
                        'PENDATANG' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('kartuKeluarga.no_kk')
                    ->label('No. KK')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Kelurahan')
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
                    ->formatStateUsing(fn (string $state): string => Respondent::VERIFICATION_STATUSES[$state] ?? $state),
                Tables\Columns\ImageColumn::make('ktp_image_path')
                    ->label('KTP')
                    ->circular(),
                Tables\Columns\TextColumn::make('responses_count')
                    ->label('Survey')
                    ->counts('responses')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options(Respondent::VERIFICATION_STATUSES),
                Tables\Filters\SelectFilter::make('citizen_type_id')
                    ->label('Jenis Warga')
                    ->relationship('citizenType', 'name'),
                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(Respondent::GENDERS),
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
                    ->visible(fn (Respondent $record): bool => $record->verification_status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn (Respondent $record) => $record->update([
                        'verification_status' => 'verified',
                        'verified_at' => now(),
                        'verified_by' => auth()->id(),
                    ])),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Respondent $record): bool => $record->verification_status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(fn (Respondent $record, array $data) => $record->update([
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
            'index' => Pages\ListRespondents::route('/'),
            'create' => Pages\CreateRespondent::route('/create'),
            'view' => Pages\ViewRespondent::route('/{record}'),
            'edit' => Pages\EditRespondent::route('/{record}/edit'),
        ];
    }
}
