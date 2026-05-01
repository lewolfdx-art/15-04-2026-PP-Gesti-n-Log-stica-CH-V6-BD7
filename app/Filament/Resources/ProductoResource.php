<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Gestión de Contenido';
    protected static ?int $navigationSort = 2;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Producto')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Producto')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->disabled()
                            ->maxLength(255)
                            ->helperText('Se genera automáticamente'),
                        
                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'concreto_premezclado' => 'Concreto Premezclado',
                                'concreto_estructural' => 'Concreto Estructural',
                                'concreto_arquitectonico' => 'Concreto Arquitectónico',
                                'aditivos' => 'Aditivos',
                                'servicios' => 'Servicios',
                            ])
                            ->required(),
                        
                        Forms\Components\RichEditor::make('description')
                            ->label('Descripción')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagen del Producto')
                            ->image()
                            ->directory('productos')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('450')
                            ->columnSpanFull(),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Precio')
                                    ->numeric()
                                    ->prefix('S/')
                                    ->step(0.01),
                                
                                Forms\Components\TextInput::make('stock')
                                    ->label('Stock')
                                    ->numeric()
                                    ->default(0),
                            ]),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Producto Activo')
                            ->default(true),
                    ]),
                
                Forms\Components\Section::make('Especificaciones Técnicas')
                    ->schema([
                        Forms\Components\Repeater::make('technical_specs')
                            ->label('Especificaciones')
                            ->schema([
                                Forms\Components\TextInput::make('spec_name')
                                    ->label('Especificación')
                                    ->required(),
                                Forms\Components\TextInput::make('spec_value')
                                    ->label('Valor')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ]),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'concreto_premezclado' => 'Concreto Premezclado',
                        'concreto_estructural' => 'Concreto Estructural',
                        'concreto_arquitectonico' => 'Concreto Arquitectónico',
                        'aditivos' => 'Aditivos',
                        'servicios' => 'Servicios',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('PEN')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'concreto_premezclado' => 'Concreto Premezclado',
                        'concreto_estructural' => 'Concreto Estructural',
                        'concreto_arquitectonico' => 'Concreto Arquitectónico',
                        'aditivos' => 'Aditivos',
                        'servicios' => 'Servicios',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}