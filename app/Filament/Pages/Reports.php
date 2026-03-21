<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Reports extends Page
{
     protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.reports';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $navigationGroup = 'Analytics';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'Admin';
    }

     protected static function getPolicies(): array
    {
        return [
            self::class => \App\Policies\ReportsPagePolicy::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\AdminWidget::class,
            \App\Filament\Widgets\LatestOrders::class,
            \App\Filament\Widgets\TopProduct::class,
            \App\Filament\Widgets\LowStockAlart::class,
            \App\Filament\Widgets\TopCustomers::class,
    ];

    }

    }
