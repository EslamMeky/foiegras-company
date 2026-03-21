<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PopularProductResource\Pages;
use App\Filament\Resources\PopularProductResource\RelationManagers;
use App\Models\PopularProduct;
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

class PopularProductResource extends Resource
{
    protected static ?string $model = PopularProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $label = 'Popular Product';
    protected static ?string $pluralLabel = 'Popular Product';

    protected static ?string $navigationGroup = 'Categories';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title_ar')
                ->label('Title (Arabic)')
                ->required(),
                TextInput::make('title_en')
                ->label('Title (English)')
                ->required(),
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

                TextColumn::make('title_ar')
                ->label('Title(AR)')
                ->toggleable()
                ->searchable()
                ->sortable(),
                TextColumn::make('title_en')
                ->label('Title(En)')
                ->toggleable()
                ->searchable()
                ->sortable(),

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
                Tables\Actions\EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),

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
            'index' => Pages\ListPopularProducts::route('/'),
            'create' => Pages\CreatePopularProduct::route('/create'),
            'edit' => Pages\EditPopularProduct::route('/{record}/edit'),
        ];
    }
}
