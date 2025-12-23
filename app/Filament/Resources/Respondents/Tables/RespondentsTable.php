<?php

namespace App\Filament\Resources\Respondents\Tables;

use App\Models\Resident;
use Illuminate\Support\Facades\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RespondentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();

                if (!$user || $user->canAccessAllOpds()) {
                    return $query;
                }

                if (Schema::hasColumn('respondents', 'opd_id')) {
                    return $query->where('opd_id', $user->opd_id);
                }

                return $query; // if no opd_id column, no scoping applied
            })
            ->columns([
                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('citizenType.name')
                    ->label('Tipe Warga')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->formatStateUsing(fn($state) => $state === 'L' ? '♂️' : '♀️')
                    ->alignCenter(),
                TextColumn::make('village.name')
                    ->label('Kelurahan')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('district.name')
                    ->label('Kecamatan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('HP')
                    ->icon('heroicon-o-phone')
                    ->toggleable(),
                TextColumn::make('verification_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn($state) => Resident::VERIFICATION_STATUSES[$state] ?? $state),
                TextColumn::make('phone_verified_at')
                    ->label('📱 OTP')
                    ->dateTime('d/m/y')
                    ->placeholder('❌')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options(Resident::VERIFICATION_STATUSES),
                SelectFilter::make('citizen_type_id')
                    ->label('Tipe Warga')
                    ->relationship('citizenType', 'name'),
                SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(Resident::GENDERS),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
