<?php

namespace App\Filament\Resources\KartuKeluargas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KartuKeluargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_kk')
                    ->searchable(),
                TextColumn::make('kepala_keluarga')
                    ->searchable(),
                TextColumn::make('rt')
                    ->searchable(),
                TextColumn::make('rw')
                    ->searchable(),
                TextColumn::make('kode_pos')
                    ->searchable(),
                TextColumn::make('province.name')
                    ->searchable(),
                TextColumn::make('regency.name')
                    ->searchable(),
                TextColumn::make('district.name')
                    ->searchable(),
                TextColumn::make('village.name')
                    ->searchable(),
                ImageColumn::make('kk_image_path'),
                TextColumn::make('verification_status')
                    ->searchable(),
                TextColumn::make('verified_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
