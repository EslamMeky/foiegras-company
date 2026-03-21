<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FavouriteResource\Pages;
use App\Filament\Resources\FavouriteResource\RelationManagers;
use App\Models\Favourite;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FavouriteResource extends Resource
{
    protected static ?string $model = Favourite::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $label = 'Favourite Product';
    protected static ?string $pluralLabel = 'Favourite Product';

    protected static ?string $navigationGroup = 'Products';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form;
        //     ->schema([
        //         //
        //     ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->sortable()
                ->toggleable(),
                TextColumn::make('user.name')
                ->label('Name')
                ->searchable()
                ->toggleable(),
                TextColumn::make('product.name_ar')
                ->label('Product Name(AR)')
                ->searchable()
                ->toggleable(),
                 TextColumn::make('product.name_en')
                ->label('Product Name(EN)')
                ->searchable()
                ->toggleable(),
             TextColumn::make('created_at')
                ->label('published')
                ->sortable()
                ->searchable()
                ->dateTime()
                ->toggleable(),
            ])->defaultSort('created_at','desc')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFavourites::route('/'),
            // 'create' => Pages\CreateFavourite::route('/create'),
            // 'edit' => Pages\EditFavourite::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
