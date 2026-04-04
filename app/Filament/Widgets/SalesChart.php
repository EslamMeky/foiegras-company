<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    // protected static ?string $heading = 'Chart';
    // protected static bool $isDiscovered = false;
    protected static ?int $sort = 2;
     protected static ?string $heading = 'Sales Last 7 Days';

    protected function getData(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            $data[] = Order::whereDate('created_at', $date)
                ->where('order_status', 'delivered')
                ->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $data,
                ],
            ],
            'labels' => [
                '6 days ago',
                '5 days ago',
                '4 days ago',
                '3 days ago',
                '2 days ago',
                'Yesterday',
                'Today',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
