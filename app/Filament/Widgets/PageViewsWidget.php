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
        $totalViews     = PageView::count();
        $todayViews     = PageView::whereDate('created_at', today())->count();
        $yesterdayViews = PageView::whereDate('created_at', today()->subDay())->count();
        $weekViews      = PageView::where('created_at', '>=', now()->subWeek())->count();
        $uniqueVisitors = PageView::distinct('ip')->count('ip');

        $trend = $yesterdayViews > 0
            ? round((($todayViews - $yesterdayViews) / $yesterdayViews) * 100, 1)
            : ($todayViews > 0 ? 100 : 0);

        return [
            Stat::make('Total Views', number_format($totalViews))
                ->description('All time page views')
                ->descriptionIcon('heroicon-o-eye')
                ->color('primary'),

            Stat::make('Today', $todayViews)
                ->description($trend >= 0 ? "+{$trend}% vs yesterday" : "{$trend}% vs yesterday")
                ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trend >= 0 ? 'success' : 'danger'),

            Stat::make('This Week', number_format($weekViews))
                ->description('Last 7 days')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('primary'),

            Stat::make('Unique Visitors', number_format($uniqueVisitors))
                ->description('By IP address')
                ->descriptionIcon('heroicon-o-finger-print')
                ->color('primary'),
        ];
    }
}
