<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertisingResource\Pages;
use App\Filament\Resources\AdvertisingResource\RelationManagers;
use App\Models\Advertising;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AdvertisingResource extends Resource
{
    protected static ?string $model = Advertising::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
            protected static ?string $navigationGroup = 'Land Page';

    protected static ?int $navigationSort = 2;

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
                TextInput::make('desc_ar')
                ->label('Description (Arabic)')
                ->required(),
                TextInput::make('desc_en')
                ->label('Description (English)')
                ->required(),
                TextInput::make('btn_ar')
                ->label('SubTitle (Arabic)')
                ->required(),
                TextInput::make('btn_en')
                ->label('SubTitle (English)')
                ->required(),
                TextInput::make('link')
                ->label('Link')
                ,
                Toggle::make('status')
                ->label('Status')
                ->required(),
                 FileUpload::make('image')
                 ->label('Image')
               ->required()
                    ->disk('advertising')
                    ->directory('') // إحنا بالفعل في المسار `/public/assets/images/category/` بسبب إعداد disk
                    ->preserveFilenames()
                    ->nullable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create')
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                     return $file->getClientOriginalName(); // احفظ فقط اسم الملف
                 })
                 ->afterStateUpdated(function ($state, $component, $livewire) {
                        if (! $livewire->record) return;

                        $oldImage = $livewire->record->getRawOriginal('image'); // القيمة الخام من قاعدة البيانات

                       if ($oldImage && $state && $oldImage !== $state) {
                          Storage::disk('advertising')->delete($oldImage);
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
                ->searchable()
                ->toggleable(),
                ImageColumn::make('image')
                ->label('Image')
                ->getStateUsing(fn ($record) => $record->image) // هيرجع الرابط من Accessor اللي في الموديل
                ->height(50)
                ->width(50)
                ->circular()
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
                TextColumn::make('desc_ar')
                ->label('Description(AR)')
                ->extraAttributes([
                'style' => 'width: 400px; height: 100px; display: inline-block; overflow: hidden; white-space: normal; word-break: break-word;',
                ])
                ->toggleable()
                ->searchable()
                ->sortable(),
                TextColumn::make('desc_en')
                ->label('Description(EN)')
                ->extraAttributes([
                'style' => 'width: 400px; height: 100px; display: inline-block; overflow: hidden; white-space: normal; word-break: break-word;',
                ])
                ->toggleable()
                ->searchable()
                ->sortable(),
                 TextColumn::make('btn_ar')
                ->label('SubTitle (Ar)')
                ->toggleable()
                ->searchable(),
                TextColumn::make('btn_en')
                ->label('SubTitle (EN)')
                ->toggleable()
                ->searchable(),
                TextColumn::make('link')
                ->label('Link')
                ->toggleable()
                ->searchable(),
                ToggleColumn::make('status'),

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
                DeleteAction::make()->iconButton()
                ->before(function (\App\Models\advertising $record) {
                 if ($record->getRawOriginal('image')) {
                 $image = $record->getRawOriginal('image'); // ناخد الاسم الأصلي بدون getImageAttribute
                 $imagePath = public_path('assets/images/advertising/' . $image);

                 if (File::exists($imagePath)) {
                     File::delete($imagePath);
                 }
        }
    })
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
            'index' => Pages\ListAdvertisings::route('/'),
            'create' => Pages\CreateAdvertising::route('/create'),
            'edit' => Pages\EditAdvertising::route('/{record}/edit'),
        ];
    }
}
