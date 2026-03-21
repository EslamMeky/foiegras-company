<?php

namespace App\Filament\Resources\PopularProductResource\Pages;

use App\Filament\Resources\PopularProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPopularProducts extends ListRecords
{
    protected static string $resource = PopularProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
