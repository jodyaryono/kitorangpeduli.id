<?php

namespace App\Filament\Resources\Questionnaires\Tables;

use App\Models\Questionnaire;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionnairesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();

                if (!$user || $user->canAccessAllOpds()) {
                    return $query;
                }

                return $query->where('opd_id', $user->opd_id);
            })
            ->columns([
                TextColumn::make('opd.name')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                BadgeColumn::make('visibility')
                    ->label('Visibility')
                    ->colors([
                        'success' => 'both',
                        'info' => 'officer_assisted',
                        'gray' => 'self_entry',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'officer_assisted' => 'Officer Only',
                        'both' => 'Both',
                        default => 'Self Entry',
                    }),
                ImageColumn::make('cover_image_path'),
                TextColumn::make('cover_video_path')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                IconColumn::make('requires_location')
                    ->boolean(),
                IconColumn::make('requires_verified_respondent')
                    ->boolean(),
                TextColumn::make('max_responses')
                    ->numeric()
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
