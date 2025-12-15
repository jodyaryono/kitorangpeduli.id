<?php

namespace App\Filament\Resources\Questionnaires;

use App\Filament\Resources\Questionnaires\Pages\CreateQuestionnaire;
use App\Filament\Resources\Questionnaires\Pages\EditQuestionnaire;
use App\Filament\Resources\Questionnaires\Pages\ListQuestionnaires;
use App\Filament\Resources\Questionnaires\Schemas\QuestionnaireForm;
use App\Filament\Resources\Questionnaires\Tables\QuestionnairesTable;
use App\Models\Questionnaire;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuestionnaireResource extends Resource
{
    protected static ?string $model = Questionnaire::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return QuestionnaireForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionnairesTable::configure($table);
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
            'index' => ListQuestionnaires::route('/'),
            'create' => CreateQuestionnaire::route('/create'),
            'edit' => EditQuestionnaire::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['admin', 'opd_admin', 'viewer']);
    }

    public static function canView($record): bool
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'opd_admin', 'viewer'])) {
            return false;
        }

        // Admin bisa lihat semua, OPD Admin dan Viewer hanya bisa lihat questionnaire OPD mereka
        if ($user->role === 'admin') {
            return true;
        }

        return $record->opd_id === $user->opd_id;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['admin', 'opd_admin']);
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'opd_admin'])) {
            return false;
        }

        // Admin bisa edit semua, OPD Admin hanya bisa edit questionnaire OPD mereka
        if ($user->role === 'admin') {
            return true;
        }

        return $record->opd_id === $user->opd_id;
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'opd_admin'])) {
            return false;
        }

        // Admin bisa hapus semua, OPD Admin hanya bisa hapus questionnaire OPD mereka
        if ($user->role === 'admin') {
            return true;
        }

        return $record->opd_id === $user->opd_id;
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['admin', 'opd_admin']);
    }
}
