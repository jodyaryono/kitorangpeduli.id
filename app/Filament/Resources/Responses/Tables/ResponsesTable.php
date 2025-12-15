<?php

namespace App\Filament\Resources\Responses\Tables;

use App\Models\Response;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ResponsesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                if (!$user) {
                    return $query;
                }

                if ($user->canAccessAllOpds()) {
                    return $query;
                }

                return $query->whereHas('questionnaire', fn($q) => $q->where('opd_id', $user->opd_id));
            })
            ->columns([
                TextColumn::make('questionnaire.title')
                    ->searchable(),
                TextColumn::make('respondent.id')
                    ->searchable(),
                BadgeColumn::make('entry_method')
                    ->label('Metode Entry')
                    ->colors([
                        'success' => fn(Response $record) => $record->entered_by_user_id !== null,
                        'gray' => fn(Response $record) => $record->entered_by_user_id === null,
                    ])
                    ->formatStateUsing(fn(Response $record) => $record->entered_by_user_id
                        ? 'Officer: ' . ($record->enteredBy?->name ?? 'Unknown')
                        : 'Self-entry'),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('lastQuestion.id')
                    ->searchable(),
                TextColumn::make('progress_percentage')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('device_info')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->searchable(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
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
                IconColumn::make('is_valid')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('entry_method')
                    ->label('Metode Entry')
                    ->options([
                        'officer' => 'Officer-Assisted',
                        'self' => 'Self-entry',
                    ])
                    ->query(function ($query, $state) {
                        if ($state === 'officer') {
                            return $query->whereNotNull('entered_by_user_id');
                        }
                        if ($state === 'self') {
                            return $query->whereNull('entered_by_user_id');
                        }
                        return $query;
                    }),
                SelectFilter::make('my_entries')
                    ->label('Entri Saya')
                    ->options([
                        'mine' => 'Hanya entri saya',
                    ])
                    ->query(function ($query, $state) {
                        if ($state === 'mine') {
                            $userId = auth()->id();
                            if ($userId) {
                                return $query->where('entered_by_user_id', $userId);
                            }
                        }
                        return $query;
                    }),
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
