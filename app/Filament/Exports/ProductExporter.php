<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('barcode'),
            ExportColumn::make('category.title_ar'),
            ExportColumn::make('category.title_en'),
            ExportColumn::make('name_ar'),
            ExportColumn::make('name_en'),
            ExportColumn::make('desc_ar'),
            ExportColumn::make('desc_en'),
            ExportColumn::make('main_price'),
            ExportColumn::make('price_discount'),
            ExportColumn::make('stock'),
            ExportColumn::make('outOfStock'),
            ExportColumn::make('weight'),
            ExportColumn::make('note'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
