<?php

namespace App\Filament\Resources\Products;

use App\Enums\ProductStatusEnum;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use App\RolesEnum;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Builder as FormBuilder;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

// use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')
                ->live(onBlur: true)
                ->required()
                ->afterStateUpdated(function (string $operation, $state, callable $set) {
                    $set('slug', Str::slug($state));
                }),
            TextInput::make('slug')
                ->required(),
            Select::make('department_id')
                ->relationship('department', 'name')
                ->label(__('Department'))
                ->preload()
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function (callable $set) {
                    $set('category_id', null);
                }),
            Select::make('category_id')
                ->relationship(
                    name: 'category',
                    titleAttribute: 'name',
                    modifyQueryUsing: function (EloquentBuilder $query, callable $get) {
                        $departmentId = $get('department_id');
                        if ($departmentId) {
                            $query->where('department_id', $departmentId);
                        }
                    }
                )
                ->label(__('Category'))
                ->preload()
                ->searchable()
                ->required(),

            RichEditor::make('description')
                ->required()
                ->toolbarButtons([
                    'blockquote',
                    'bold',
                    'bulletList',
                    'h2',
                    'h3',
                    'italic',
                    'link',
                    'orderedList',
                    'redo',
                    'strike',
                    'underline',
                    'undo',
                    'table'
                ])
                ->columnSpan(2),
            TextInput::make('price')
                ->required()
                ->numeric(),
            TextInput::make('quantity')
                ->numeric(),
            Select::make('status')
                ->options(ProductStatusEnum::labels())
                ->default(ProductStatusEnum::Draft->value)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')
                ->sortable()
                ->words(10)
                ->searchable(),
            TextColumn::make('status')
                ->badge()
                ->colors(ProductStatusEnum::colors()),
            TextColumn::make('department.name'),
            TextColumn::make('category.name'),
            TextColumn::make('created_at')->dateTime()
                ->dateTime(),
        ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(ProductStatusEnum::labels()),
                SelectFilter::make('department_id')
                    ->relationship('department', 'name')
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])
            ])
        ;
    }

    // public static function form(Schema $schema): Schema
    // {
    //     return ProductForm::configure($schema);
    // }

    // public static function table(Table $table): Table
    // {
    //     return ProductsTable::configure($table);
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasRole(RolesEnum::Vendor);
    }
}
