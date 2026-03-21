<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerReviewResource\Pages;
use App\Filament\Resources\CustomerReviewResource\RelationManagers;
use App\Models\CustomerReview;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerReviewResource extends Resource
{
    protected static ?string $model = CustomerReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $label = 'Customer Reviews';
    protected static ?string $pluralLabel = 'Customer Reviews';
            protected static ?string $navigationGroup = 'Customer Reviews';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form;
            // ->schema([
            //     //
            // ]);
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
            ])->defaultSort('created_at','desc')

            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
               Tables\Actions\DeleteAction::make()->iconButton()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListCustomerReviews::route('/'),
            // 'create' => Pages\CreateCustomerReview::route('/create'),
            // 'edit' => Pages\EditCustomerReview::route('/{record}/edit'),
        ];

    }

    public static function canCreate(): bool
    {
        return false;
    }
}
