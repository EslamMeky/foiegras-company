<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

        protected static ?string $navigationGroup = 'Setting';

    protected static ?int $navigationSort = 13;

    // protected static bool $shouldSkipAuthorization = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 TextInput::make('name')
                ->label('Name')
                ->required(),
                TextInput::make('phone')
                ->label('Phone')
                ->required(),
                Select::make('gender')
                ->label('Gender')
                ->required()
                ->options([
                    'Male'=>'Male',
                    'Female'=>'Female',
                ]),
                 Select::make('role')
                ->label('Role')
                ->required()
                ->options([
                    'Admin'=>'Admin',
                    'Editor'=>'Editor',
                    'Client'=>'Client',
                ]),
                TextInput::make('address')
                ->label('Address'),
                TextInput::make('email')
                ->label('email')
                ->email()
                ->required(),
                TextInput::make('password')
                ->password()
                ->required()
                ->visibleOn('create')
                ->revealable(),
                FileUpload::make('image')
                    ->label('Image')
                    // ->required()
                    ->disk('user')
                    ->directory('') // إحنا بالفعل في المسار `/public/assets/images/category/` بسبب إعداد disk
                    ->preserveFilenames()
                    ->nullable()
                    ->dehydrated(fn ($state) => filled($state))
                    // ->required(fn (string $context) => $context === 'create')
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                     return $file->getClientOriginalName(); // احفظ فقط اسم الملف
                 })
                 ->afterStateUpdated(function ($state, $component, $livewire) {
                        if (! $livewire->record) return;

                        $oldImage = $livewire->record->getRawOriginal('image'); // القيمة الخام من قاعدة البيانات

                       if ($oldImage && $state && $oldImage !== $state) {
                          Storage::disk('user')->delete($oldImage);
                      }
                 }),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->sortable()
                ->toggleable(),
                TextColumn::make('name')
                ->sortable()
                ->searchable()
                ->toggleable(),
                ImageColumn::make('image')
                ->label('image')
                ->getStateUsing(fn ($record) => $record->image) // هيرجع الرابط من Accessor اللي في الموديل
                ->height(50)
                ->width(50)
                ->circular()
                ->toggleable(),
                TextColumn::make('phone')
                ->searchable()
                ->toggleable(),
                TextColumn::make('address')
                ->label('Address')
                ->searchable()
                ->toggleable(),
                TextColumn::make('email')
                ->searchable()
                ->toggleable(),
                TextColumn::make('gender')
                ->toggleable(),
                TextColumn::make('role')
                ->toggleable()
                ->badge()
                ->color(function(string $state): string{
                    return match($state){
                        'Admin'=>'danger',
                        'Client'=>'info',
                        'Editor'=>'success'
                    };
                }),
                TextColumn::make('created_at')
                ->label('published')
                ->sortable()
                ->searchable()
                ->dateTime()
                ->toggleable(),

            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
