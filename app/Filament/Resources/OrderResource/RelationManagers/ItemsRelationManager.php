<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form;
            // ->schema([
            //     Forms\Components\TextInput::make('product')
            //         ->required()
            //         ->maxLength(255),
            // ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('product')
            ->columns([
               TextColumn::make('product.name_en')
                ->label('Product'),

                TextColumn::make('quantity'),

                TextColumn::make('price')
                    ->money('EGP'),

                TextColumn::make('subtotal')
                    ->money('EGP'),
            ])
            ->filters([
                //
            ])->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }

}
