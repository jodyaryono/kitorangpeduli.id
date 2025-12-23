<?php

namespace App\Filament\Pages;

use App\Models\Resident;
use App\Models\Response;
use Filament\Pages\Page;

class ResponseMap extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected string $view = 'filament.pages.response-map';

    protected static ?string $navigationLabel = 'Peta Responden';

    protected static ?string $title = 'Peta Lokasi Responden';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    public function getViewData(): array
    {
        $responses = Response::with(['resident', 'questionnaire'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', 'completed')
            ->get()
            ->map(function ($response) {
                return [
                    'id' => $response->id,
                    'lat' => (float) $response->latitude,
                    'lng' => (float) $response->longitude,
                    'name' => $response->respondent->nama_lengkap,
                    'questionnaire' => $response->questionnaire->title,
                    'date' => $response->completed_at?->format('d M Y H:i'),
                ];
            });

        $respondents = Respondent::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('citizenType')
            ->get()
            ->map(function ($respondent) {
                return [
                    'id' => $respondent->id,
                    'lat' => (float) $respondent->latitude,
                    'lng' => (float) $respondent->longitude,
                    'name' => $respondent->nama_lengkap,
                    'citizenType' => $respondent->citizenType?->name ?? 'Unknown',
                    'village' => $respondent->village?->name ?? '',
                ];
            });

        return [
            'responses' => $responses,
            'respondents' => $respondents,
        ];
    }
}
