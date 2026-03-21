<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;
    protected static ?int $sort = 1;
     protected function getStats(): array
    {
        return [
             Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->icon('heroicon-o-users')
                ->color('success')
                ->chart([1,3,35,15,20,30,50,80,100]),

            Stat::make('Total Products', Product::count())
                ->description('Products in store')
                ->icon('heroicon-o-shopping-bag')
                ->color('info')
                ->chart([1,3,5,15,60,30,50,80,100]),

            Stat::make('Total Orders', Order::count())
                ->description('Orders placed')
                ->icon('heroicon-o-shopping-cart')
                ->color('primary')
                ->chart([1,3,5,15,20,30,50,80,100]),

            // Stat::make('Total Revenue', Order::sum('total') . ' EGP')
            //     ->description('All sales revenue')
            //     ->icon('heroicon-o-banknotes')
            //     ->color('danger')
            //     ->chart([1,3,5,20,30,50,80,100]),

            Stat::make('Pending Orders', Order::where('order_status', 'pending')->count())
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->description('All Pending Orders')

        ];


    }

}
