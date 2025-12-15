<?php

namespace App\Filament\Resources\KartuKeluargaResource\Pages;

use App\Filament\Resources\KartuKeluargaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewKartuKeluarga extends ViewRecord
{
    protected static string $resource = KartuKeluargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Data Kartu Keluarga')
                    ->schema([
                        Infolists\Components\TextEntry::make('no_kk')
                            ->label('Nomor KK')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('kepala_keluarga')
                            ->label('Kepala Keluarga'),
                    ])->columns(2),

                Infolists\Components\Section::make('Alamat')
                    ->schema([
                        Infolists\Components\TextEntry::make('alamat')
                            ->label('Alamat'),
                        Infolists\Components\TextEntry::make('rt')
                            ->label('RT'),
                        Infolists\Components\TextEntry::make('rw')
                            ->label('RW'),
                        Infolists\Components\TextEntry::make('village.name')
                            ->label('Kelurahan'),
                        Infolists\Components\TextEntry::make('district.name')
                            ->label('Kecamatan'),
                        Infolists\Components\TextEntry::make('regency.name')
                            ->label('Kabupaten/Kota'),
                        Infolists\Components\TextEntry::make('province.name')
                            ->label('Provinsi'),
                    ])->columns(3),

                Infolists\Components\Section::make('Dokumen')
                    ->schema([
                        Infolists\Components\ImageEntry::make('kk_image_path')
                            ->label('Foto KK')
                            ->height(400),
                    ]),

                Infolists\Components\Section::make('Status Verifikasi')
                    ->schema([
                        Infolists\Components\TextEntry::make('verification_status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'verified' => 'success',
                                'rejected' => 'danger',
                                default => 'warning',
                            }),
                        Infolists\Components\TextEntry::make('verifiedBy.name')
                            ->label('Diverifikasi Oleh'),
                        Infolists\Components\TextEntry::make('verified_at')
                            ->label('Waktu Verifikasi')
                            ->dateTime('d M Y H:i'),
                        Infolists\Components\TextEntry::make('verification_notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }
}
