<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpdResource\Pages;
use App\Models\Opd;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class OpdResource extends Resource
{
    protected static ?string $model = Opd::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office';

    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'OPD';

    protected static ?string $pluralModelLabel = 'OPD';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('Informasi OPD')
                    ->schema([
                        Components\TextInput::make('code')
                            ->label('Kode OPD')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Components\TextInput::make('name')
                            ->label('Nama OPD')
                            ->required()
                            ->maxLength(200),
                        Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                        Components\TextInput::make('address')
                            ->label('Alamat')
                            ->maxLength(255),
                        Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(20),
                        Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),
                        Components\FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->directory('opd-logos')
                            ->maxSize(2048),
                        Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama OPD')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('questionnaires_count')
                    ->label('Kuesioner')
                    ->counts('questionnaires')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpds::route('/'),
            'create' => Pages\CreateOpd::route('/create'),
            'edit' => Pages\EditOpd::route('/{record}/edit'),
        ];
    }
}
