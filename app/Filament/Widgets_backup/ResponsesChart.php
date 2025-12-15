<?php

namespace App\Filament\Widgets;

use App\Models\Response;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ResponsesChart extends ChartWidget
{
    protected static ?string $heading = 'Jawaban Survey per Bulan';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Response::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];

        // Generate last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->format('M Y');
            
            $found = $data->firstWhere('month', $month);
            $values[] = $found ? $found->count : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jawaban Survey',
                    'data' => $values,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
