<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\Feature;
use App\Models\PopularProduct;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Categories';

    protected static ?int $navigationSort = 6;

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

                Select::make('features_ids')
                ->label('Feature')
                ->multiple()
                ->options(Feature::pluck('title_en','id'))
                ->searchable()
                ->preload(),

                Select::make('popular_products_ids')
                ->label('Popular Product')
                ->multiple()
                ->options(PopularProduct::pluck('title_en','id'))
                ->searchable()
                ->preload(),

                 FileUpload::make('image')
                 ->label('Image')
               ->required()
                    ->disk('category')
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
                          Storage::disk('category')->delete($oldImage);
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

       TagsColumn::make('features_ids')
            ->label('Features')
            ->getStateUsing(function ($record) {
                $ids = $record->features_ids ?? [];
                return \App\Models\Feature::whereIn('id', $ids)
                    ->pluck('title_en')
                    ->toArray();
            }),

        TagsColumn::make('popular_products_ids')
                ->label('Popular Products')
                ->getStateUsing(function ($record) {
                    $ids = $record->popular_products_ids ?? [];
                    return \App\Models\PopularProduct::whereIn('id', $ids)
                        ->pluck('title_en')
                        ->toArray();
                }),

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
                 $imagePath = public_path('assets/images/category/' . $image);

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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
