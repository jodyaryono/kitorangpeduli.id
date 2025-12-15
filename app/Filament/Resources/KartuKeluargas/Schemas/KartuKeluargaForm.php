<?php

namespace App\Filament\Resources\KartuKeluargas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class KartuKeluargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no_kk')
                    ->required(),
                TextInput::make('kepala_keluarga')
                    ->required(),
                Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('rt'),
                TextInput::make('rw'),
                TextInput::make('kode_pos'),
                Select::make('province_id')
                    ->relationship('province', 'name'),
                Select::make('regency_id')
                    ->relationship('regency', 'name'),
                Select::make('district_id')
                    ->relationship('district', 'name'),
                Select::make('village_id')
                    ->relationship('village', 'name'),
                FileUpload::make('kk_image_path')
                    ->image(),
                TextInput::make('verification_status')
                    ->required()
                    ->default('pending'),
                TextInput::make('verified_by')
                    ->numeric(),
                DateTimePicker::make('verified_at'),
                Textarea::make('rejection_reason')
                    ->columnSpanFull(),
                Textarea::make('verification_notes')
                    ->columnSpanFull(),
            ]);
    }
}
