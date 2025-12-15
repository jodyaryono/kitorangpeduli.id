<?php

namespace App\Filament\Widgets;

use App\Models\Respondent;
use Filament\Widgets\ChartWidget;

class CitizenTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Warga';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Respondent::selectRaw('citizen_type_id, COUNT(*) as count')
            ->with('citizenType')
            ->groupBy('citizen_type_id')
            ->get();

        $labels = [];
        $values = [];
        $colors = [
            'rgba(34, 197, 94, 0.8)',   // OAP - green
            'rgba(59, 130, 246, 0.8)',   // PORTNUMBAY - blue
            'rgba(245, 158, 11, 0.8)',   // WNA - amber
            'rgba(107, 114, 128, 0.8)',  // PENDATANG - gray
        ];

        $i = 0;
        foreach ($data as $item) {
            $labels[] = $item->citizenType?->name ?? 'Unknown';
            $values[] = $item->count;
            $i++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Responden',
                    'data' => $values,
                    'backgroundColor' => array_slice($colors, 0, count($values)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
