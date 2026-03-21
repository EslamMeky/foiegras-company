<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

        protected static ?string $navigationGroup = 'Products';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
             Select::make('category_id')
                ->relationship('category','title_en')
                ->required()
                ->label('Category')
                ->placeholder('Category Name'),
                TextInput::make('name_ar')
                ->required()
                ->label('product name (Arabic)'),
                TextInput::make('name_en')
                ->required()
                ->label('product name (English)'),
                TextInput::make('desc_ar')
                ->required()
                ->label('product description (Arabic)'),
                TextInput::make('desc_en')
                ->required()
                ->label('product description (English)'),
                TextInput::make('main_price')
                ->nullable()
                ->label('Price Discount')
                ->numeric(),
                TextInput::make('price_discount')
                ->required()
                ->label('Main Price')
                ->numeric(),
                TextInput::make('stock')
                ->required()
                ->label('stock')
                ->numeric(),
                TextInput::make('barcode')
                ->nullable()
                ->label('Barcode'),
                // TextInput::make('weight')
                // ->required()
                // ->label('product Weight'),


            Forms\Components\Repeater::make('weight')
                ->label('Product Weights')
                ->schema([
                    TextInput::make('weight')
                        ->label('Weight')
                        ->required(),
                    TextInput::make('price')
                        ->label('Price for this weight')
                        ->required()
                        ->numeric(),
                ])
                ->columns(2) // يبقى الوزن والـ price جنب بعض
                ->createItemButtonLabel('Add Weight Option')
                ->minItems(1)
                ->required(),

                TextInput::make('note')
                ->nullable()
                ->label('product Notes'),

                FileUpload::make('image')
                    ->label('Main Image')
                    ->required()
                    ->disk('product')
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
                          Storage::disk('product')->delete($oldImage);
                      }
                 }),

                    // FileUpload::make('otherImage')
                    // ->label('Other Images')
                    // ->disk('product')
                    // ->directory('')
                    // ->preserveFilenames()
                    // ->multiple() // ← الصور المتعددة
                    // ->reorderable() // ← لو عايز ترتب الصور
                    // ->nullable()
                    // ->dehydrated(fn ($state) => filled($state)) // يخزن فقط لما يكون فيه قيمة
                    // ->required(fn (string $context) => $context === 'create')
                    // ->getUploadedFileNameForStorageUsing(function ($file) {
                    //     return $file->getClientOriginalName(); // يحتفظ بالاسم الأصلي
                    // })
                    // ->afterStateUpdated(function ($state, $component, $livewire) {
                    //     if (! $livewire->record) return;

                    //    $oldImages = json_decode($livewire->record->getRawOriginal('otherImage'), true) ?? [];


                    //     // نحذف الصور اللي اتشالت يدويًا
                    //     if (is_array($oldImages) && is_array($state)) {
                    //         $deleted = collect($oldImages)->diff($state);
                    //         foreach ($deleted as $file) {
                    //             Storage::disk('product')->delete($file);
                    //         }
                    //     }
                    // }),

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
                ->label('image')
                ->getStateUsing(fn ($record) => $record->image) // هيرجع الرابط من Accessor اللي في الموديل
                ->height(50)
                ->width(50)
                ->circular()
                ->toggleable(),
                TextColumn::make('category.title_en')
                ->label('Category Name'),
                TextColumn::make('name_ar')
                ->label('Name(AR)')
                ->toggleable()
                ->searchable(),
                TextColumn::make('name_en')
                ->label('Name(EN)')
                ->toggleable()
                ->searchable(),
                TextColumn::make('desc_ar')
                ->label('Description(AR)')
                ->toggleable()
                ->searchable(),
                TextColumn::make('desc_en')
                ->label('Description(EN)')
                ->toggleable()
                ->searchable(),
                TextColumn::make('main_price')
                ->label('Price Discount')
                ->toggleable()
                ->searchable(),
                TextColumn::make('price_discount')
                ->label('Main Price')
                ->toggleable()
                ->searchable(),
                TextColumn::make('stock')
                ->label('Stock')
                ->toggleable(),
                // TextColumn::make('outOfStock')
                // ->label('Out Of Stock')
                // ->toggleable(),


                // TextColumn::make('weight')
                // ->label('Weight')
                // ->toggleable(),

                TagsColumn::make('weight')
    ->label('Weights')
    ->getStateUsing(function ($record) {
        $weights = $record->weight ?? []; // المفروض JSON مفكك كـ array
        return collect($weights)->map(function ($item) {
            $w = $item['weight'] ?? '';
            $p = $item['price'] ?? '';
            return "{$w} - {$p}";
        })->toArray();
    }),
                TextColumn::make('note')
                ->label('Notes')
                ->toggleable(),
                TextColumn::make('barcode')
                ->label('Barcode')
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
                Tables\Actions\EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton()
                ->before(function (\App\Models\product $record) {
             if ($record->getRawOriginal('image')) {
            $image = $record->getRawOriginal('image'); // ناخد الاسم الأصلي بدون getImageAttribute
            $imagePath = public_path('assets/images/product/' . $image);

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
              }
            })


            ])
            ->headerActions([
                ExportAction::make()
                ->exporter(ProductExporter::class)
                ->formats([
                    ExportFormat::Csv
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->exporter(ProductExporter::class)
                    ->formats([
                          ExportFormat::Csv
                    ])
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
