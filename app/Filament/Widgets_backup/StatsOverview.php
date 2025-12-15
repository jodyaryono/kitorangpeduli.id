<?php

namespace App\Filament\Widgets;

use App\Models\Respondent;
use App\Models\KartuKeluarga;
use App\Models\Response;
use App\Models\Questionnaire;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRespondents = Respondent::count();
        $verifiedRespondents = Respondent::verified()->count();
        $pendingVerification = Respondent::pending()->count();
        $totalKK = KartuKeluarga::count();
        $verifiedKK = KartuKeluarga::verified()->count();
        $totalResponses = Response::count();
        $completedResponses = Response::completed()->count();
        $activeQuestionnaires = Questionnaire::active()->count();

        return [
            Stat::make('Total Responden', $totalRespondents)
                ->description($verifiedRespondents . ' terverifikasi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            Stat::make('Kartu Keluarga', $totalKK)
                ->description($verifiedKK . ' terverifikasi')
                ->descriptionIcon('heroicon-m-home')
                ->color('info'),
            Stat::make('Menunggu Verifikasi', $pendingVerification)
                ->description('Responden & KK')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Total Jawaban Survey', $completedResponses)
                ->description('dari ' . $activeQuestionnaires . ' kuesioner aktif')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}
