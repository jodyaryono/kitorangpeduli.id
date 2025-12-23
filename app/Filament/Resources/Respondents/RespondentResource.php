<?php

namespace App\Filament\Resources\Respondents;

use App\Filament\Resources\Respondents\Pages\CreateRespondent;
use App\Filament\Resources\Respondents\Pages\EditRespondent;
use App\Filament\Resources\Respondents\Pages\ListRespondents;
use App\Filament\Resources\Respondents\Pages\ViewRespondent;
use App\Filament\Resources\Respondents\Schemas\RespondentForm;
use App\Filament\Resources\Respondents\Schemas\RespondentInfolist;
use App\Filament\Resources\Respondents\Tables\RespondentsTable;
use App\Models\Resident;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RespondentResource extends Resource
{
    protected static ?string $model = Resident::class;

    protected static ?string $slug = 'residents';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Residents';

    protected static ?string $modelLabel = 'Resident';

    protected static ?string $pluralModelLabel = 'Residents';

    protected static string|\UnitEnum|null $navigationGroup = 'Responden';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return RespondentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RespondentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RespondentsTable::configure($table);
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
            'index' => ListRespondents::route('/'),
            'create' => CreateRespondent::route('/create'),
            'view' => ViewRespondent::route('/{record}'),
            'edit' => EditRespondent::route('/{record}/edit'),
        ];
    }
}
