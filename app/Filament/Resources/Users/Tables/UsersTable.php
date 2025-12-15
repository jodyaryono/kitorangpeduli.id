<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();

                if (!$user) {
                    return $query;
                }

                // OPD Admin hanya bisa lihat user di OPD mereka sendiri
                if ($user->isOpdAdmin() && $user->opd_id) {
                    return $query->where('opd_id', $user->opd_id);
                }

                return $query;
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->placeholder('-'),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->colors([
                        'danger' => 'admin',
                        'warning' => 'opd_admin',
                        'success' => 'field_officer',
                        'info' => 'viewer',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'admin' => 'Administrator',
                        'opd_admin' => 'OPD Admin',
                        'field_officer' => 'Field Officer',
                        'viewer' => 'Viewer',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('opd.name')
                    ->label('OPD')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Semua OPD'),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Administrator',
                        'opd_admin' => 'OPD Admin',
                        'field_officer' => 'Field Officer',
                        'viewer' => 'Viewer',
                    ])
                    ->native(false),
                SelectFilter::make('opd_id')
                    ->label('OPD')
                    ->relationship('opd', 'name')
                    ->searchable()
                    ->native(false),
                Filter::make('is_active')
                    ->label('Hanya User Aktif')
                    ->query(fn(Builder $query): Builder => $query->where('is_active', true))
                    ->default(),
                Filter::make('is_inactive')
                    ->label('Hanya User Nonaktif')
                    ->query(fn(Builder $query): Builder => $query->where('is_active', false)),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function ($record) {
                        $userId = auth()->id();
                        // Mencegah hapus user yang sedang login
                        if ($record->id === $userId) {
                            throw new \Exception('Tidak dapat menghapus user yang sedang login');
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            $userId = auth()->id();
                            // Mencegah hapus user yang sedang login
                            if ($records->contains('id', $userId)) {
                                throw new \Exception('Tidak dapat menghapus user yang sedang login');
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
