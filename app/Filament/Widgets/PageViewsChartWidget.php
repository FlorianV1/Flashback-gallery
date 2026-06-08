<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;

class PageViewsChartWidget extends ChartWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Page Views — Last 30 Days';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $days  = 30;
        $start = now()->subDays($days - 1)->startOfDay();

        $viewsByDate = PageView::query()
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->where('created_at', '>=', $start)
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $data   = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date     = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('M j');
            $data[]   = (int) ($viewsByDate->get($date, 0));
        }

        return [
            'datasets' => [
                [
                    'label'            => 'Page Views',
                    'data'             => $data,
                    'borderColor'      => '#E85D04',
                    'backgroundColor'  => 'rgba(232, 93, 4, 0.08)',
                    'fill'             => true,
                    'tension'          => 0.4,
                    'pointRadius'      => 3,
                    'pointHoverRadius' => 5,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['precision' => 0],
                ],
            ],
        ];
    }
}
