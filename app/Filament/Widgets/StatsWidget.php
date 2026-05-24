<?php

namespace App\Filament\Widgets;

use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        return [
            Stat::make('Albums', Album::count())
                ->description('Total albums in the gallery')
                ->descriptionIcon('heroicon-o-photo')
                ->color('primary'),

            Stat::make('Photos', Photo::count())
                ->description('Total uploaded photos')
                ->descriptionIcon('heroicon-o-camera')
                ->color('primary'),

            Stat::make('Contributors', User::where('role', 'contributor')->count())
                ->description('People with access to upload')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),
        ];
    }
}
