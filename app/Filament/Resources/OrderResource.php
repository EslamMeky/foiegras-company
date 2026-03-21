<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
            protected static ?string $navigationGroup = 'Orders';

    protected static ?int $navigationSort = 12;

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
                ->label('Order ID')
                ->sortable(),

            TextColumn::make('user.name')
                ->label('Client'),
            TextColumn::make('customer_name')
                ->label('Customer Name'),

            TextColumn::make('customer_phone'),

            TextColumn::make('payment_method')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                'paymob' => 'warning',
                'cod' => 'success',
                }),

            TextColumn::make('payment_status')
            ->badge()
            ->color(fn (string $state): string => match ($state) {
                'pending' => 'warning',
                'paid' => 'success',
                'failed' => 'danger',
            }),
            TextColumn::make('order_status')
            ->badge()
            ->color(fn (string $state): string => match ($state) {

                'pending' => 'warning',
                'processing' => 'info',
                'shipped' => 'primary',
                'delivered' => 'success',
                'cancelled' => 'danger',

            }),

            TextColumn::make('total')
                ->money('EGP')
                ->badge()
                ->color('success'),

            TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton(),

                Tables\Actions\Action::make('processing')
                ->label('Processing')
                ->action(fn ($record) => $record->update(['order_status'=>'processing'])),

                Tables\Actions\Action::make('shipped')
                    ->label('Shipped')
                    ->action(fn ($record) => $record->update(['order_status'=>'shipped'])),

                Tables\Actions\Action::make('delivered')
                    ->label('Delivered')
                    ->action(fn ($record) => $record->update(['order_status'=>'delivered'])),
                            ]);
            // ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                // ]),
            // ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // 'create' => Pages\CreateOrder::route('/create'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([

            TextEntry::make('id')
                ->label('Order ID'),

            TextEntry::make('user.name')
                ->label('Client'),
                // ->toggleable()
                // ->searchable(),
            TextEntry::make('customer_name')
                ->label('Customer Name'),
                // ->toggleable()
                // ->searchable(),

            TextEntry::make('customer_phone'),
                // ->toggleable()
                // ->searchable(),

            TextEntry::make('shipping_address'),

            TextEntry::make('payment_method')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                'paymob' => 'warning',
                'cod' => 'success',
                }),
                // ->toggleable(),

            TextEntry::make('payment_status')
                // ->toggleable()
                // ->searchable()
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                'pending' => 'warning',
                'paid' => 'success',
                'failed' => 'danger',
            }),


            TextEntry::make('order_status')
                // ->toggleable()
                // ->searchable()
                ->badge()
                ->color(fn (string $state): string => match ($state) {

                'pending' => 'warning',
                'processing' => 'info',
                'shipped' => 'primary',
                'delivered' => 'success',
                'cancelled' => 'danger',

            }),


            TextEntry::make('subtotal')
                ->money('EGP'),
                // ->toggleable()
                // ->searchable(),
            TextEntry::make('shipping_fee')
                ->money('EGP'),
                // ->toggleable()
                // ->searchable(),

            TextEntry::make('total')
                ->money('EGP')
                ->badge()
                ->color('success'),
                // ->toggleable()
                // ->searchable(),

            TextEntry::make('created_at')
                ->dateTime(),
                // ->toggleable()
                // ->searchable(),

        ]);
    }
    public static function canCreate(): bool
    {
        return false;
    }
    // public static function canEdit($record): bool
    // {
    //     return false; // يبطل أي زرار Save Changes أو Edit
    // }
}
