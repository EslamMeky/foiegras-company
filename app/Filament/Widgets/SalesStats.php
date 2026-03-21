<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class SalesStats extends BaseWidget
{
    protected static bool $isDiscovered = false;
   protected function getCards(): array
    {
        return [

            Card::make('Total Orders', Order::count())
            ->description('All registered users')
                ->icon('heroicon-o-users')
                ->color('success')
                ->chart([1,3,35,15,20,30,50,80,100]),

            Card::make(
                'Total Revenue',
                Order::sum('total')
            )->description('EGP'),

            Card::make(
                'Total Products',
                Product::count()
            ),

            Card::make(
                'Total Users',
                User::count()
            ),

        ];
    }
}
