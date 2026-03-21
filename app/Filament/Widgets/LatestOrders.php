<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;


class LatestOrders extends TableWidget
{
    protected static bool $isDiscovered = false;
    protected function getTableQuery() : Builder
    {
        return Order::latest()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('user.name')
            ->label('Custmer Name')
            ->searchable(),
            Tables\Columns\TextColumn::make('total')->label('Total'),
            TextColumn::make('payment_method')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                'paymob' => 'warning',
                'cod' => 'success',
                })
                ->toggleable(),

            TextColumn::make('payment_status')
                ->toggleable()
                ->searchable()
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                'pending' => 'warning',
                'paid' => 'success',
                'failed' => 'danger',
            }),

            Tables\Columns\TextColumn::make('order_status')
            ->label('Status')
            ->searchable()
            ->badge()
            ->color(fn (string $state): string => match ($state) {

                'pending' => 'warning',
                'processing' => 'info',
                'shipped' => 'primary',
                'delivered' => 'success',
                'cancelled' => 'danger',
                }),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [

            Tables\Filters\Filter::make('created_at')
                ->form([
                    DatePicker::make('from'),
                    DatePicker::make('until'),
                ])
                ->query(function ($query, array $data) {

                    return $query
                        ->when(
                            $data['from'],
                            fn ($query) => $query->whereDate('created_at', '>=', $data['from'])
                        )
                        ->when(
                            $data['until'],
                            fn ($query) => $query->whereDate('created_at', '<=', $data['until'])
                        );
                }),

        ];
    }
}
