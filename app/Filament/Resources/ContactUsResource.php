<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactUsResource\Pages;
use App\Filament\Resources\ContactUsResource\RelationManagers;
use App\Models\ContactUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactUsResource extends Resource
{
    protected static ?string $model = ContactUs::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone-arrow-down-left';
    protected static ?string $label = 'Contact us';
    protected static ?string $pluralLabel = 'Contact us';
            protected static ?string $navigationGroup = 'Land Page';

    protected static ?int $navigationSort = 3;

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
                ->toggleable()
                ->sortable()
                ->searchable(),
                TextColumn::make('name')
                ->label('Name')
                ->toggleable()
                ->sortable()
                ->searchable(),
                TextColumn::make('email')
                ->label('Email')
                ->toggleable()
                ->sortable()
                ->searchable(),
                TextColumn::make('subject')
                ->label('Subject')
                ->toggleable()
                ->sortable()
                ->searchable(),
                TextColumn::make('message')
                ->label('Message')
                ->toggleable()
                ->sortable()
                ->searchable(),
                TextColumn::make('created_at')
                ->label('Published')
                ->toggleable()
                ->dateTime()

            ])
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
            'index' => Pages\ListContactUs::route('/'),
            // 'create' => Pages\CreateContactUs::route('/create'),
            // 'edit' => Pages\EditContactUs::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
