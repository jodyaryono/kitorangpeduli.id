<?php

namespace App\Filament\Widgets;

use App\Models\Family;
use App\Models\Questionnaire;
use App\Models\Resident;
use App\Models\Response;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalResidents = Resident::count();
        $verifiedResidents = Resident::verified()->count();
        $pendingVerification = Resident::pending()->count();
        $totalFamilies = Family::count();
        $verifiedFamilies = Family::verified()->count();
        $totalResponses = Response::count();
        $completedResponses = Response::completed()->count();
        $activeQuestionnaires = Questionnaire::active()->count();

        return [
            Stat::make('Total Responden', $totalResidents)
                ->description($verifiedResidents . ' terverifikasi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            Stat::make('Keluarga', $totalFamilies)
                ->description($verifiedFamilies . ' terverifikasi')
                ->descriptionIcon('heroicon-m-home')
                ->color('info'),
            Stat::make('Menunggu Verifikasi', $pendingVerification)
                ->description('Responden & Keluarga')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Total Jawaban Survey', $completedResponses)
                ->description('dari ' . $activeQuestionnaires . ' kuesioner aktif')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}
