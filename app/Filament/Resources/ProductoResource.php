<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ProductoResource\Pages\ListProductos;
use App\Filament\Resources\ProductoResource\Pages\CreateProducto;
use App\Filament\Resources\ProductoResource\Pages\EditProducto;
use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Contenido';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Productos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Configuración de Productos')
                    ->tabs([
                        Tab::make('Configuración')
                            ->schema([
                                TextInput::make('page_title')
                                    ->label('Título de la página')
                                    ->default('Nuestros Productos y Servicios')
                                    ->maxLength(255),
                                
                                RichEditor::make('page_description')
                                    ->label('Descripción de la página')
                                    ->default('Ofrecemos concreto premezclado y servicios complementarios para garantizar la excelencia en cada obra.')
                                    ->toolbarButtons(['bold', 'italic', 'underline']),
                                
                                Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Solo un registro puede estar activo a la vez'),
                            ]),
                        
                        // ===================== CATEGORÍA 1: TIPOS DE CONCRETO =====================
                        Tab::make('Tipos de Concreto')
                            ->schema([
                                Repeater::make('tipos_concreto')
                                    ->label('Lista de Tipos de Concreto')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->placeholder('Ej: Convencionales'),
                                        RichEditor::make('description')
                                            ->label('Descripción')
                                            ->required()
                                            ->placeholder('Describe este tipo de concreto...'),
                                        FileUpload::make('image')
                                            ->label('Imagen')
                                            ->image()
                                            ->directory('productos')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9'),
                                        TextInput::make('price')
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
                        
                        // ===================== CATEGORÍA 2: SERVICIOS COMPLEMENTARIOS =====================
                        Tab::make('Servicios Complementarios')
                            ->schema([
                                Repeater::make('servicios_complementarios')
                                    ->label('Lista de Servicios Complementarios')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->placeholder('Ej: Venta de Concreto Premezclado'),
                                        RichEditor::make('description')
                                            ->label('Descripción')
                                            ->required()
                                            ->placeholder('Describe este servicio...'),
                                        FileUpload::make('image')
                                            ->label('Imagen')
                                            ->image()
                                            ->directory('productos')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9'),
                                        TextInput::make('price')
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
                                        ['name' => 'Servicio de Bombeo', 'description' => 'Servicio de bombeo con pluma y estacionaria, para vaciados en zonas de difícil acceso.'],
                                    ]),
                            ]),
                        
                        // ===================== CATEGORÍA 3: PAVIMENTOS =====================
                        Tab::make('Pavimentos')
                            ->schema([
                                Repeater::make('pavimentos')
                                    ->label('Pavimentos')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->placeholder('Ej: Pavimento Rígido'),
                                        RichEditor::make('description')
                                            ->label('Descripción')
                                            ->required()
                                            ->placeholder('Describe este tipo de pavimento...'),
                                        FileUpload::make('image')
                                            ->label('Imagen')
                                            ->image()
                                            ->directory('productos')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9'),
                                    ])
                                    ->columns(1)
                                    ->collapsible()
                                    ->default([
                                        ['name' => 'Pavimento Rígido', 'description' => 'Losas de concreto hidráulico para vías urbanas, carreteras y áreas industriales.'],
                                        ['name' => 'Pavimento Flexible', 'description' => 'Estructura asfáltica con base de concreto para mayor durabilidad.'],
                                    ]),
                            ]),
                        
                        // ===================== APLICACIONES CONSTRUCTIVAS =====================
                        Tab::make('Aplicaciones Constructivas')
                            ->schema([
                                Section::make('Aplicaciones para Estructuras')
                                    ->schema([
                                        Repeater::make('aplicaciones')
                                            ->label('Aplicaciones Constructivas')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Nombre')
                                                    ->required()
                                                    ->placeholder('Ej: Zapatas y Cimientos'),
                                                RichEditor::make('description')
                                                    ->label('Descripción')
                                                    ->required()
                                                    ->placeholder('Describe esta aplicación...'),
                                                FileUpload::make('image')
                                                    ->label('Imagen')
                                                    ->image()
                                                    ->directory('productos/aplicaciones')
                                                    ->imageResizeMode('cover')
                                                    ->imageCropAspectRatio('16:9'),
                                            ])
                                            ->columns(1)
                                            ->collapsible()
                                            ->default([
                                                ['name' => 'Zapatas y Cimientos', 'description' => 'Elementos estructurales que transmiten las cargas de la edificación al suelo.'],
                                                ['name' => 'Columnas', 'description' => 'Elementos verticales de concreto armado que soportan cargas de compresión.'],
                                                ['name' => 'Losas', 'description' => 'Elementos horizontales que conforman los pisos y techos de las edificaciones.'],
                                            ]),
                                    ]),
                            ]),
                        
                        // ===================== SECCIÓN DE MÉTRICAS =====================
                        Tab::make('Métricas')
                            ->schema([
                                Section::make('Estadísticas de la Empresa')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('metric_proyectos')
                                                    ->label('Proyectos realizados')
                                                    ->numeric()
                                                    ->default(35)
                                                    ->suffix('+'),
                                                
                                                TextInput::make('metric_concreto')
                                                    ->label('m³ de concreto')
                                                    ->numeric()
                                                    ->default(47)
                                                    ->suffix('K+'),
                                                
                                                TextInput::make('metric_mixers')
                                                    ->label('Mixers modernos')
                                                    ->numeric()
                                                    ->default(15)
                                                    ->suffix('+'),
                                                
                                                TextInput::make('metric_experiencia')
                                                    ->label('Años de experiencia')
                                                    ->numeric()
                                                    ->default(10)
                                                    ->suffix('+'),
                                            ]),
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
                TextColumn::make('page_title')
                    ->label('Título de página')
                    ->searchable()
                    ->limit(30),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => ListProductos::route('/'),
            'create' => CreateProducto::route('/create'),
            'edit' => EditProducto::route('/{record}/edit'),
        ];
    }
}