<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopCustomers extends BaseWidget
{
    protected static bool $isDiscovered = false;
   protected function getTableQuery(): Builder
    {
        return User::query()
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderByDesc('orders_sum_total');
    }

    protected function getTableColumns(): array
    {
        return [

            Tables\Columns\TextColumn::make('name')
            ->searchable(),

            Tables\Columns\TextColumn::make('email')
            ->searchable(),

            Tables\Columns\TextColumn::make('orders_count')
                ->label('Total Orders'),

            Tables\Columns\TextColumn::make('orders_sum_total')
                ->label('Total Spent')
                ->money('EGP'),

        ];
    }
}
