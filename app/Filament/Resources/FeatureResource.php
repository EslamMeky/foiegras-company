<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeatureResource\Pages;
use App\Filament\Resources\FeatureResource\RelationManagers;
use App\Models\Feature;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $label = 'Fetures Category';
    protected static ?string $pluralLabel = 'Fetures Category';

    protected static ?string $navigationGroup = 'Categories';


    protected static ?int $navigationSort = 7;


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
         EditAction::make()->iconButton(),
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
            'index' => Pages\ListFeatures::route('/'),
            'create' => Pages\CreateFeature::route('/create'),
            'edit' => Pages\EditFeature::route('/{record}/edit'),
        ];
    }
}
