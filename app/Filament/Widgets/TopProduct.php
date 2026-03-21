<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopProduct extends BaseWidget
{
    protected static bool $isDiscovered = false;
   protected function getTableQuery(): Builder
    {
        return Product::withSum('orderItems','quantity')
            ->orderByDesc('order_items_sum_quantity')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name_en')
            ->label('Name')
            ->toggleable()
            ->searchable(),
            Tables\Columns\TextColumn::make('order_items_sum_quantity')
                ->label('Sold Quantity'),
            Tables\Columns\TextColumn::make('price_discount')->label('Price')->money('EGP'),
            Tables\Columns\TextColumn::make('stock')
        ];
    }
}
