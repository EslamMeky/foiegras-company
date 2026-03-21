<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutResource\Pages;
use App\Filament\Resources\AboutResource\RelationManagers;
use App\Models\About;
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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AboutResource extends Resource
{
    protected static ?string $model = About::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $label = 'About us';
    protected static ?string $pluralLabel = 'About us';
        protected static ?string $navigationGroup = 'Land Page';

    protected static ?int $navigationSort = 1;

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
                FileUpload::make('image')
                ->label('Image')
                ->required()
                ->disk('about')
                ->directory('')
                ->preserveFilenames()
                ->nullable()->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create')
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                     return $file->getClientOriginalName(); // احفظ فقط اسم الملف
                 })
                 ->afterStateUpdated(function ($state, $component, $livewire) {
                        if (! $livewire->record) return;

                        $oldImage = $livewire->record->getRawOriginal('image'); // القيمة الخام من قاعدة البيانات

                       if ($oldImage && $state && $oldImage !== $state) {
                          Storage::disk('about')->delete($oldImage);
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
                ->label('image')
                ->getStateUsing(fn ($record) => $record->image) // هيرجع الرابط من Accessor اللي في الموديل
                ->height(50)
                ->width(50)
                ->circular()
                ->toggleable(),
                TextColumn::make('title_ar')
                ->label('Title(Ar)')
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
                ->label('Description(En)')
                ->extraAttributes([
                'style' => 'width: 400px; height: 100px; display: inline-block; overflow: hidden; white-space: normal; word-break: break-word;',
                ])
                ->toggleable()
                ->searchable()
                ->sortable(),

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
                         Tables\Actions\EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton()
                ->before(function (\App\Models\about $record) {
                 if ($record->getRawOriginal('image')) {
                 $image = $record->getRawOriginal('image'); // ناخد الاسم الأصلي بدون getImageAttribute
                 $imagePath = public_path('assets/images/about/' . $image);

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
            'index' => Pages\ListAbouts::route('/'),
            'create' => Pages\CreateAbout::route('/create'),
            'edit' => Pages\EditAbout::route('/{record}/edit'),
        ];
    }
}
