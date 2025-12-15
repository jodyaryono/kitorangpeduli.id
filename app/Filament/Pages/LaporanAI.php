<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class LaporanAI extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = '🤖 Laporan AI';

    protected static ?string $title = 'Laporan AI';

    protected static ?string $slug = 'laporan-ai';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 100;

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public static function getNavigationUrl(array $parameters = []): string
    {
        return route('laporan-ai.index');
    }
}
