<?php

namespace App\Filament\Resources\KartuKeluargas\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KartuKeluargaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('no_kk'),
                TextEntry::make('kepala_keluarga'),
                TextEntry::make('alamat')
                    ->columnSpanFull(),
                TextEntry::make('rt')
                    ->placeholder('-'),
                TextEntry::make('rw')
                    ->placeholder('-'),
                TextEntry::make('kode_pos')
                    ->placeholder('-'),
                TextEntry::make('province.name')
                    ->label('Province')
                    ->placeholder('-'),
                TextEntry::make('regency.name')
                    ->label('Regency')
                    ->placeholder('-'),
                TextEntry::make('district.name')
                    ->label('District')
                    ->placeholder('-'),
                TextEntry::make('village.name')
                    ->label('Village')
                    ->placeholder('-'),
                ImageEntry::make('kk_image_path')
                    ->placeholder('-'),
                TextEntry::make('verification_status'),
                TextEntry::make('verified_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('rejection_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('verification_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
