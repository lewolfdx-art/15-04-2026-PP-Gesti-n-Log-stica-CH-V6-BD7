<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Repeater;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Gestión de Contenido';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Configuración de Productos')
                    ->tabs([
                        Tabs\Tab::make('Configuración')
                            ->schema([
                                Forms\Components\TextInput::make('page_title')
                                    ->label('Título de la página')
                                    ->default('Nuestros Productos y Servicios')
                                    ->maxLength(255),
                                
                                Forms\Components\RichEditor::make('page_description')
                                    ->label('Descripción de la página')
                                    ->default('Ofrecemos concreto premezclado y servicios complementarios para garantizar la excelencia en cada obra.')
                                    ->toolbarButtons(['bold', 'italic', 'underline']),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Solo un registro puede estar activo a la vez'),
                            ]),
                        
                        Tabs\Tab::make('Tipos de Concreto')
                            ->schema([
                                Repeater::make('tipos_concreto')
                                    ->label('Lista de Tipos de Concreto')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->placeholder('Ej: Convencionales'),
                                        Forms\Components\RichEditor::make('description')
                                            ->label('Descripción')
                                            ->required()
                                            ->placeholder('Describe este tipo de concreto...'),
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Imagen')
                                            ->image()
                                            ->directory('productos')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9'),
                                        Forms\Components\TextInput::make('price')
                                            ->label('Precio (opcional)')
                                            ->numeric()
                                            ->prefix('S/')
                                            ->step(0.01),
                                    ])
                                    ->columns(1)
                                    ->collapsible()
                                    ->default([
                                        ['name' => 'Convencionales', 'description' => 'Para vaciado de elementos estructurales en general. Resistencia desde 100 hasta 350 kg/cm², controlados a 28 días.'],
                                        ['name' => 'Fragua Controlada', 'description' => 'Elaborados según condiciones de obra, con retardo o acelerante de fraguado según necesidad.'],
                                        ['name' => 'Temprana Resistencia', 'description' => 'Para estructuras que requieren habilitación rápida tras vaciado. Control estricto de materiales.'],
                                        ['name' => 'Alta Resistencia', 'description' => 'Resistencia superior a 400 kg/cm². Usado en obras de gran exigencia.'],
                                        ['name' => 'Rheoplástico', 'description' => 'Concreto de alta fluidez (slump >8") ideal para vaciados con refuerzo denso.'],
                                        ['name' => 'Lanzado', 'description' => 'Aplicado mediante presión neumática, adherencia y compactación inmediata.'],
                                    ]),
                            ]),
                        
                        Tabs\Tab::make('Servicios Complementarios')
                            ->schema([
                                Repeater::make('servicios_complementarios')
                                    ->label('Lista de Servicios Complementarios')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->placeholder('Ej: Venta de Concreto Premezclado'),
                                        Forms\Components\RichEditor::make('description')
                                            ->label('Descripción')
                                            ->required()
                                            ->placeholder('Describe este servicio...'),
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Imagen')
                                            ->image()
                                            ->directory('productos')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9'),
                                        Forms\Components\TextInput::make('price')
                                            ->label('Precio (opcional)')
                                            ->numeric()
                                            ->prefix('S/')
                                            ->step(0.01),
                                    ])
                                    ->columns(1)
                                    ->collapsible()
                                    ->default([
                                        ['name' => 'Venta de Concreto Premezclado', 'description' => 'Concretos de diferentes resistencias y características, controlados en laboratorio.'],
                                        ['name' => 'Alquiler de Mixer', 'description' => 'Mixers modernos de 8 a 12 m³, disponibles para entrega en cualquier punto de la región.'],
                                        ['name' => 'Bomba de Concreto', 'description' => 'Servicio de bombeo con pluma y estacionaria, para vaciados en zonas de difícil acceso.'],
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->activeTab(0),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page_title')
                    ->label('Título de página')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
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