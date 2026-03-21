<?php

namespace App\Filament\Resources\PopularProductResource\Pages;

use App\Filament\Resources\PopularProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPopularProduct extends EditRecord
{
    protected static string $resource = PopularProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
