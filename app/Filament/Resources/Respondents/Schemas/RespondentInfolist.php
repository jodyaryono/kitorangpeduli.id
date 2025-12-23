<?php

namespace App\Filament\Resources\Respondents\Schemas;

use App\Models\Resident;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RespondentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Identitas
                TextEntry::make('nik')
                    ->label('NIK')
                    ->copyable()
                    ->weight('bold'),
                TextEntry::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->weight('bold'),
                TextEntry::make('citizenType.name')
                    ->label('Tipe Warga')
                    ->badge()
                    ->color('success'),
                TextEntry::make('status_hubungan')
                    ->label('Status Keluarga')
                    ->placeholder('-'),
                // Data Pribadi
                TextEntry::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->placeholder('-'),
                TextEntry::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->date('d F Y')
                    ->placeholder('-'),
                TextEntry::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn($state) => Resident::GENDERS[$state] ?? $state)
                    ->placeholder('-'),
                TextEntry::make('golongan_darah')
                    ->label('Golongan Darah')
                    ->placeholder('-'),
                TextEntry::make('agama')
                    ->label('Agama')
                    ->placeholder('-'),
                TextEntry::make('status_perkawinan')
                    ->label('Status Perkawinan')
                    ->placeholder('-'),
                TextEntry::make('pendidikan')
                    ->label('Pendidikan')
                    ->placeholder('-'),
                TextEntry::make('pekerjaan')
                    ->label('Pekerjaan')
                    ->placeholder('-'),
                TextEntry::make('kewarganegaraan')
                    ->label('Kewarganegaraan')
                    ->formatStateUsing(fn($state) => Resident::NATIONALITIES[$state] ?? $state),
                // Alamat
                TextEntry::make('alamat')
                    ->label('Alamat')
                    ->columnSpanFull()
                    ->placeholder('-'),
                TextEntry::make('rt')
                    ->label('RT')
                    ->placeholder('-'),
                TextEntry::make('rw')
                    ->label('RW')
                    ->placeholder('-'),
                TextEntry::make('village.name')
                    ->label('Kelurahan/Desa')
                    ->placeholder('-'),
                TextEntry::make('district.name')
                    ->label('Kecamatan')
                    ->placeholder('-'),
                TextEntry::make('regency.name')
                    ->label('Kabupaten/Kota')
                    ->placeholder('-'),
                TextEntry::make('province.name')
                    ->label('Provinsi')
                    ->placeholder('-'),
                // GPS
                TextEntry::make('latitude')
                    ->label('Latitude')
                    ->copyable()
                    ->placeholder('-'),
                TextEntry::make('longitude')
                    ->label('Longitude')
                    ->copyable()
                    ->placeholder('-'),
                // Kontak
                TextEntry::make('phone')
                    ->label('No. HP')
                    ->prefix('+62')
                    ->copyable()
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email')
                    ->copyable()
                    ->placeholder('-'),
                TextEntry::make('phone_verified_at')
                    ->label('HP Terverifikasi')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum'),
                // Verifikasi
                TextEntry::make('verification_status')
                    ->label('Status Verifikasi')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn($state) => Resident::VERIFICATION_STATUSES[$state] ?? $state),
                TextEntry::make('verifier.name')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('-'),
                TextEntry::make('verified_at')
                    ->label('Waktu Verifikasi')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-'),
                TextEntry::make('rejection_reason')
                    ->label('Alasan Penolakan')
                    ->columnSpanFull()
                    ->placeholder('-'),
                // Dokumen
                ImageEntry::make('ktp_image_path')
                    ->label('Foto KTP')
                    ->height(150),
                ImageEntry::make('selfie_ktp_path')
                    ->label('Selfie KTP')
                    ->height(150),
                // Metadata
                TextEntry::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i'),
                TextEntry::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y, H:i'),
            ]);
    }
}

