<?php

namespace App\Filament\Resources\KartuKeluargas;

use App\Filament\Resources\KartuKeluargas\Pages\CreateKartuKeluarga;
use App\Filament\Resources\KartuKeluargas\Pages\EditKartuKeluarga;
use App\Filament\Resources\KartuKeluargas\Pages\ListKartuKeluargas;
use App\Filament\Resources\KartuKeluargas\Pages\ViewKartuKeluarga;
use App\Filament\Resources\KartuKeluargas\Schemas\KartuKeluargaForm;
use App\Filament\Resources\KartuKeluargas\Schemas\KartuKeluargaInfolist;
use App\Filament\Resources\KartuKeluargas\Tables\KartuKeluargasTable;
use App\Models\Family;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KartuKeluargaResource extends Resource
{
    protected static ?string $model = Family::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Families';

    protected static ?string $modelLabel = 'Family';

    protected static ?string $pluralModelLabel = 'Families';

    protected static string|\UnitEnum|null $navigationGroup = 'Responden';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return KartuKeluargaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KartuKeluargaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KartuKeluargasTable::configure($table);
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
            'index' => ListKartuKeluargas::route('/'),
            'create' => CreateKartuKeluarga::route('/create'),
            'view' => ViewKartuKeluarga::route('/{record}'),
            'edit' => EditKartuKeluarga::route('/{record}/edit'),
        ];
    }
}
