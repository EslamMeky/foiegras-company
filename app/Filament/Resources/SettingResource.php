<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $navigationGroup = 'Setting';

    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

               FileUpload::make('logo')
                    ->label('Logo')
                    // ->required()
                    ->disk('setting')
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

                        $oldImage = $livewire->record->getRawOriginal('logo'); // القيمة الخام من قاعدة البيانات

                       if ($oldImage && $state && $oldImage !== $state) {
                          Storage::disk('setting')->delete($oldImage);
                      }
                 }),


                TextInput::make('slug_ar')
                ->label('Slug (Ar)')
                ->required(),

                TextInput::make('slug_en')
                ->label('Slug (EN)')
                ->required(),

                TextInput::make('desc_ar')
                ->label('desc (Arabic)')
                ->required(),

                TextInput::make('desc_en')
                ->label('desc (En)')
                ->required(),

                TextInput::make('face')
                ->label('Link (Facebook)'),

                TextInput::make('insta')
                ->label('Link (Instgram)'),

                TextInput::make('tiktok')
                ->label('Link (Tiktok)'),

                TextInput::make('whats')
                ->label('Link (WhatsApp)'),

                TextInput::make('location_ar')
                ->label('Location (ar)')
                ->required(),

                TextInput::make('location_en')
                ->label('Location (En)')
                ->required(),

                TextInput::make('phone')
                ->label('Phone')
                ->required(),
                TextInput::make('email')
                ->label('Email')
                ->required(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->sortable()
                ->toggleable(),

                ImageColumn::make('logo')
                ->label('Logo')
                ->getStateUsing(fn ($record) => $record->logo) // هيرجع الرابط من Accessor اللي في الموديل
                ->height(50)
                ->width(50)
                ->circular()
                ->toggleable(),

                TextColumn::make('slug_ar')
                ->label('Slug (AR)')
                ->searchable()
                ->toggleable(),

                TextColumn::make('slug_en')
                ->label('Slug (EN)')
                ->searchable()
                ->toggleable(),

                TextColumn::make('desc_ar')
                ->label('Desc (AR)')
                ->searchable()
                ->toggleable(),

                TextColumn::make('desc_en')
                ->label('Desc (EN)')
                ->searchable()
                ->toggleable(),

                TextColumn::make('location_ar')
                ->label('Location (AR)')
                ->searchable()
                ->toggleable(),

                 TextColumn::make('location_en')
                ->label('Location (EN)')
                ->searchable()
                ->toggleable(),

                TextColumn::make('face')
                ->label('Link(Facebook)')
                ->searchable()
                ->toggleable(),


                TextColumn::make('insta')
                ->label('Link(Instgram)')
                ->searchable()
                ->toggleable(),


                TextColumn::make('tiktok')
                ->label('Link(Tiktok)')
                ->searchable()
                ->toggleable(),


                TextColumn::make('whats')
                ->label('Link(WhatsApp)')
                ->searchable()
                ->toggleable(),

                TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->toggleable(),
                TextColumn::make('phone')
                ->label('Phone')
                ->searchable()
                ->toggleable(),

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
                DeleteAction::make()->iconButton()
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
