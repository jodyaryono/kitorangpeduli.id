<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'User Officer';

    protected static ?string $modelLabel = 'User Officer';

    protected static ?string $pluralModelLabel = 'User Officer';

    protected static string|\UnitEnum|null $navigationGroup = 'User Maintenance';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isOpdAdmin());
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isOpdAdmin());
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Admin bisa edit semua user
        if ($user->isAdmin()) {
            return true;
        }

        // OPD Admin hanya bisa edit user di OPD mereka sendiri
        if ($user->isOpdAdmin()) {
            return $record->opd_id === $user->opd_id;
        }

        return false;
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Tidak bisa hapus diri sendiri
        if ($record->id === $user->id) {
            return false;
        }

        // Admin bisa hapus semua user
        if ($user->isAdmin()) {
            return true;
        }

        // OPD Admin hanya bisa hapus user di OPD mereka sendiri (kecuali admin)
        if ($user->isOpdAdmin()) {
            return $record->opd_id === $user->opd_id && !$record->isAdmin();
        }

        return false;
    }
}
