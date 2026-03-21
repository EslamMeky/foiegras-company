<?php

namespace App\Filament\Resources\AdvertisingResource\Pages;

use App\Filament\Resources\AdvertisingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAdvertisings extends ListRecords
{
    protected static string $resource = AdvertisingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'ALL'=>Tab::make(),
            'Published'=>Tab::make()->modifyQueryUsing(function (Builder $query){
                $query->where('status','1');
            } ),
            'Un Published'=>Tab::make()->modifyQueryUsing(function (Builder $query){
                $query->where('status','0');
            } ),
        ];
    }
}
