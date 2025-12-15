<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use App\Models\Response;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewResponse extends ViewRecord
{
    protected static string $resource = ResponseResource::class;

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
                Infolists\Components\Section::make('Informasi Survey')
                    ->schema([
                        Infolists\Components\TextEntry::make('questionnaire.title')
                            ->label('Kuesioner'),
                        Infolists\Components\TextEntry::make('questionnaire.opd.name')
                            ->label('OPD'),
                        Infolists\Components\TextEntry::make('respondent.nama_lengkap')
                            ->label('Responden'),
                        Infolists\Components\TextEntry::make('respondent.nik')
                            ->label('NIK'),
                        Infolists\Components\TextEntry::make('respondent.citizenType.name')
                            ->label('Jenis Warga')
                            ->badge(),
                    ])->columns(3),

                Infolists\Components\Section::make('Waktu & Lokasi')
                    ->schema([
                        Infolists\Components\TextEntry::make('started_at')
                            ->label('Mulai')
                            ->dateTime('d M Y H:i:s'),
                        Infolists\Components\TextEntry::make('completed_at')
                            ->label('Selesai')
                            ->dateTime('d M Y H:i:s'),
                        Infolists\Components\TextEntry::make('latitude')
                            ->label('Latitude'),
                        Infolists\Components\TextEntry::make('longitude')
                            ->label('Longitude'),
                    ])->columns(4),

                Infolists\Components\Section::make('Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'completed' => 'success',
                                'submitted' => 'primary',
                                default => 'warning',
                            }),
                        Infolists\Components\IconEntry::make('is_valid')
                            ->label('Valid')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('validation_notes')
                            ->label('Catatan Validasi'),
                    ])->columns(3),

                Infolists\Components\Section::make('Jawaban')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('answers')
                            ->schema([
                                Infolists\Components\TextEntry::make('question.question_text')
                                    ->label('Pertanyaan')
                                    ->columnSpan(2),
                                Infolists\Components\TextEntry::make('answer_text')
                                    ->label('Jawaban')
                                    ->columnSpan(2)
                                    ->formatStateUsing(fn ($state, $record) => $record->selectedOption?->option_text ?? $state ?? '-'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
