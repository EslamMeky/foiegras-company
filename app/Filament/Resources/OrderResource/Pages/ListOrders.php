<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'ALL'=>Tab::make(),
              'Processing'=>Tab::make()->modifyQueryUsing(function(EloquentBuilder $query){
                $query->where('order_status','processing');
            }),
              'Shipping'=>Tab::make()->modifyQueryUsing(function(EloquentBuilder $query){
                $query->where('order_status','shipping');
            }),
            'Delivered'=>Tab::make()->modifyQueryUsing(function(EloquentBuilder $query){
                $query->where('order_status','delivered');
            }),
        ];
    }
}
