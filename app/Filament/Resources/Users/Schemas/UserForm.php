<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Opd;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi User')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->autocomplete('name'),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->autocomplete('email'),
                                TextInput::make('phone')
                                    ->label('No. Telepon')
                                    ->tel()
                                    ->maxLength(20)
                                    ->autocomplete('tel'),
                                Select::make('role')
                                    ->label('Role')
                                    ->required()
                                    ->options(function () {
                                        $user = auth()->user();
                                        $options = [
                                            'admin' => 'Administrator',
                                            'opd_admin' => 'OPD Admin',
                                            'field_officer' => 'Field Officer',
                                            'viewer' => 'Viewer',
                                        ];

                                        // OPD Admin tidak bisa membuat user Admin
                                        if ($user && $user->isOpdAdmin()) {
                                            unset($options['admin']);
                                        }

                                        return $options;
                                    })
                                    ->native(false)
                                    ->reactive()
                                    ->default('field_officer'),
                                Select::make('opd_id')
                                    ->label('OPD')
                                    ->options(function () {
                                        $user = auth()->user();

                                        // OPD Admin hanya bisa pilih OPD mereka sendiri
                                        if ($user && $user->isOpdAdmin() && $user->opd_id) {
                                            return Opd::query()
                                                ->where('id', $user->opd_id)
                                                ->pluck('name', 'id');
                                        }

                                        return Opd::query()->active()->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->native(false)
                                    ->nullable()
                                    ->default(function () {
                                        $user = auth()->user();
                                        // Auto-set OPD untuk OPD Admin
                                        if ($user && $user->isOpdAdmin()) {
                                            return $user->opd_id;
                                        }
                                        return null;
                                    })
                                    ->disabled(function () {
                                        $user = auth()->user();
                                        // OPD Admin tidak bisa ubah OPD
                                        return $user && $user->isOpdAdmin();
                                    })
                                    ->helperText(function () {
                                        $user = auth()->user();
                                        if ($user && $user->isOpdAdmin()) {
                                            return 'User akan dibuat untuk OPD Anda';
                                        }
                                        return 'Kosongkan untuk akses semua OPD (khusus Admin)';
                                    })
                                    ->visible(fn($get) => in_array($get('role'), ['opd_admin', 'field_officer', 'viewer'])),
                                Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->inline(false)
                                    ->helperText('Nonaktifkan untuk melarang akses user ke sistem'),
                            ]),
                    ])
                    ->columns(1),
                Section::make('Password')
                    ->description('Field Officer dan Viewer login menggunakan OTP, tidak memerlukan password')
                    ->visible(fn($get) => in_array($get('role'), ['admin', 'opd_admin']))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn(string $operation, $get): bool =>
                                        $operation === 'create' && in_array($get('role'), ['admin', 'opd_admin']))
                                    ->dehydrateStateUsing(fn($state) => $state ? Hash::make($state) : null)
                                    ->dehydrated(fn($state) => filled($state))
                                    ->rule(Password::min(8))
                                    ->helperText(fn(string $operation): string => $operation === 'edit'
                                        ? 'Kosongkan jika tidak ingin mengubah password'
                                        : 'Minimal 8 karakter')
                                    ->autocomplete('new-password'),
                                TextInput::make('password_confirmation')
                                    ->label('Konfirmasi Password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn(string $operation, $get): bool =>
                                        ($operation === 'create' && in_array($get('role'), ['admin', 'opd_admin'])) || filled($get('password')))
                                    ->dehydrated(false)
                                    ->same('password')
                                    ->autocomplete('new-password'),
                            ]),
                    ])
                    ->columns(1),
            ]);
    }
}
