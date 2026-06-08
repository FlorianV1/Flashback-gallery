<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PageViewsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Views', PageView::count())
                ->description('All time page views')
                ->descriptionIcon('heroicon-o-eye')
                ->color('primary'),

            Stat::make('Today', PageView::whereDate('created_at', today())->count())
                ->description('Views today')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('This Week', PageView::where('created_at', '>=', now()->subWeek())->count())
                ->description('Last 7 days')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('primary'),

            Stat::make('Unique Visitors', PageView::distinct('ip')->count('ip'))
                ->description('By IP address')
                ->descriptionIcon('heroicon-o-finger-print')
                ->color('primary'),
        ];
    }
}
