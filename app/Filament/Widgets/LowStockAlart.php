<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockAlart extends BaseWidget
{
    protected static bool $isDiscovered = false;
    protected function getTableQuery(): Builder
    {
        return Product::where('stock','<=',5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name_en')
            ->label('Name')
            ->toggleable()
            ->searchable(),
            Tables\Columns\TextColumn::make('stock')
            ->badge()
            ->color(fn (string $state): string => match ($state) {
                '0' => 'danger',

                }),
            Tables\Columns\TextColumn::make('price_discount')->label('Price')->money('EGP'),
        ];
    }
}
