<?php

namespace App\Filament\Resources\RespondentResource\Pages;

use App\Filament\Resources\RespondentResource;
use App\Models\Respondent;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewRespondent extends ViewRecord
{
    protected static string $resource = RespondentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('verify')
                ->label('Verifikasi')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->verification_status === 'pending')
                ->requiresConfirmation()
                ->action(fn () => $this->record->update([
                    'verification_status' => 'verified',
                    'verified_at' => now(),
                    'verified_by' => auth()->id(),
                ])),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Data KTP')
                    ->schema([
                        Infolists\Components\TextEntry::make('nik')
                            ->label('NIK')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('nama_lengkap')
                            ->label('Nama Lengkap'),
                        Infolists\Components\TextEntry::make('tempat_lahir')
                            ->label('Tempat Lahir'),
                        Infolists\Components\TextEntry::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->date('d M Y'),
                        Infolists\Components\TextEntry::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn (string $state): string => Respondent::GENDERS[$state] ?? $state),
                        Infolists\Components\TextEntry::make('golongan_darah')
                            ->label('Golongan Darah'),
                        Infolists\Components\TextEntry::make('agama')
                            ->label('Agama'),
                        Infolists\Components\TextEntry::make('status_perkawinan')
                            ->label('Status Perkawinan'),
                        Infolists\Components\TextEntry::make('pekerjaan')
                            ->label('Pekerjaan'),
                        Infolists\Components\TextEntry::make('pendidikan')
                            ->label('Pendidikan'),
                    ])->columns(3),

                Infolists\Components\Section::make('Kartu Keluarga & Jenis Warga')
                    ->schema([
                        Infolists\Components\TextEntry::make('kartuKeluarga.no_kk')
                            ->label('No. KK')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('kartuKeluarga.kepala_keluarga')
                            ->label('Kepala Keluarga'),
                        Infolists\Components\TextEntry::make('status_hubungan')
                            ->label('Status Hubungan'),
                        Infolists\Components\TextEntry::make('citizenType.name')
                            ->label('Jenis Warga')
                            ->badge(),
                        Infolists\Components\TextEntry::make('kewarganegaraan')
                            ->label('Kewarganegaraan'),
                    ])->columns(3),

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
                    ])->columns(4),

                Infolists\Components\Section::make('Kontak & Lokasi')
                    ->schema([
                        Infolists\Components\TextEntry::make('phone')
                            ->label('No. WhatsApp')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('latitude')
                            ->label('Latitude'),
                        Infolists\Components\TextEntry::make('longitude')
                            ->label('Longitude'),
                    ])->columns(4),

                Infolists\Components\Section::make('Dokumen')
                    ->schema([
                        Infolists\Components\ImageEntry::make('ktp_image_path')
                            ->label('Foto KTP')
                            ->height(300),
                        Infolists\Components\ImageEntry::make('selfie_ktp_path')
                            ->label('Selfie dengan KTP')
                            ->height(300),
                    ])->columns(2),

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
