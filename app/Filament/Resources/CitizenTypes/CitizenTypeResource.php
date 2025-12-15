<?php

namespace App\Filament\Resources\CitizenTypes;

use App\Filament\Resources\CitizenTypes\Pages\CreateCitizenType;
use App\Filament\Resources\CitizenTypes\Pages\EditCitizenType;
use App\Filament\Resources\CitizenTypes\Pages\ListCitizenTypes;
use App\Filament\Resources\CitizenTypes\Schemas\CitizenTypeForm;
use App\Filament\Resources\CitizenTypes\Tables\CitizenTypesTable;
use App\Models\CitizenType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CitizenTypeResource extends Resource
{
    protected static ?string $model = CitizenType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CitizenTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitizenTypesTable::configure($table);
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
            'index' => ListCitizenTypes::route('/'),
            'create' => CreateCitizenType::route('/create'),
            'edit' => EditCitizenType::route('/{record}/edit'),
        ];
    }
}
