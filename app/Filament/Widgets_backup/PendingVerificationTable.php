<?php

namespace App\Filament\Widgets;

use App\Models\Respondent;
use App\Models\KartuKeluarga;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingVerificationTable extends BaseWidget
{
    protected static ?string $heading = 'Menunggu Verifikasi';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Respondent::query()
                    ->where('verification_status', 'pending')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('citizenType.name')
                    ->label('Jenis Warga')
                    ->badge(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('WhatsApp'),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Kelurahan'),
                Tables\Columns\ImageColumn::make('ktp_image_path')
                    ->label('KTP')
                    ->circular(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->url(fn (Respondent $record): string => route('filament.admin.resources.respondents.view', $record)),
            ]);
    }
}
