<?php

namespace App\Filament\Resources\FavouriteResource\Pages;

use App\Filament\Resources\FavouriteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFavourite extends EditRecord
{
    protected static string $resource = FavouriteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
