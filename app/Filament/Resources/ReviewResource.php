<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $label = 'Reviews Product';
    protected static ?string $pluralLabel = 'Reviews Product';

    protected static ?string $navigationGroup = 'Products';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('user_id'),
                TextInput::make('product_id'),
                TextInput::make('rating'),
                TextInput::make('review'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                 ->sortable()
                ->searchable()
                ->toggleable(),
                TextColumn::make('users.name')
                ->label('Customer Name')
                ->searchable()
                ->toggleable(),
                TextColumn::make('products.name_en')
                ->label('Product Name')
                ->searchable()
                ->toggleable(),
                TextColumn::make('rating')
                ->label('Rating')
                ->searchable()
                ->toggleable(),
                TextColumn::make('review')
                ->label('Review')
                ->searchable()
                ->toggleable(),
                TextColumn::make('created_at')
                ->label('published')
                ->sortable()
                ->searchable()
                ->dateTime()
                ->toggleable(),
            ]) ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                DeleteAction::make()->iconButton()

            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListReviews::route('/'),
            // 'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

     public static function canCreate(): bool
    {
        return false;
    }
}
