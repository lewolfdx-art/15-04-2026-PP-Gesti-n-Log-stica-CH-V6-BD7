<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\InicioResource\Pages\ListInicios;
use App\Filament\Resources\InicioResource\Pages\CreateInicio;
use App\Filament\Resources\InicioResource\Pages\EditInicio;
use App\Filament\Resources\InicioResource\Pages;
use App\Models\Inicio;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;

class InicioResource extends Resource
{
    use \App\Filament\Concerns\SoloAdministradores;

    protected static ?string $model = Inicio::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Contenido';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Página de inicio';
    protected static ?string $modelLabel = 'Página de inicio';
    protected static ?string $pluralModelLabel = 'Página de inicio';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'pagina-inicio';
    
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Configuración de Inicio')
                    ->tabs([
                        Tab::make('Hero Principal')
                            ->schema([
                                TextInput::make('hero_title')
                                    ->label('Título Principal')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Concreto Premezclado de Alta Calidad')
                                    ->columnSpanFull(),
                                
                                Textarea::make('hero_description')
                                    ->label('Descripción del Hero')
                                    ->rows(3)
                                    ->required()
                                    ->placeholder('Describe los servicios y beneficios principales de la empresa...')
                                    ->columnSpanFull(),
                                
                                FileUpload::make('hero_image')
                                    ->label('Imagen del Hero')
                                    ->image()
                                    ->directory('inicio')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->columnSpanFull(),
                                
                                Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Solo un registro puede estar activo a la vez'),
                            ])
                            ->columns(2),
                        
                        Tab::make('Botón CTA')
                            ->schema([
                                TextInput::make('cta_text')
                                    ->label('Texto del Botón')
                                    ->maxLength(255)
                                    ->placeholder('Ej: Cotizar Ahora, Solicitar Información')
                                    ->columnSpanFull(),
                                
                                TextInput::make('cta_url')
                                    ->label('URL del Botón')
                                    ->maxLength(255)
                                    ->placeholder('Ej: /contacto, /cotizar')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                        
                        Tab::make('Beneficios')
                            ->schema([
                                Repeater::make('beneficios')
                                    ->label('Lista de Beneficios')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Título')
                                            ->required()
                                            ->placeholder('Ej: Plantas Automatizadas'),
                                        TextInput::make('icon')
                                            ->label('Icono (Bootstrap)')
                                            ->placeholder('Ej: bi-gear-fill, bi-check-circle-fill')
                                            ->helperText('Ver iconos en: https://icons.getbootstrap.com/'),
                                        Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(2)
                                            ->required()
                                            ->placeholder('Describe el beneficio detalladamente...'),
                                    ])
                                    ->columns(1)
                                    ->collapsible(),
                            ]),
                        
                        Tab::make('Proyectos Recientes')
                            ->schema([
                                Repeater::make('proyectos_recientes')
                                    ->label('Lista de Proyectos')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Título del Proyecto')
                                            ->required()
                                            ->placeholder('Ej: Centro de Salud Militar N°31'),
                                        Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(2)
                                            ->required()
                                            ->placeholder('Ej: Huancayo – Obra hospitalaria'),
                                            FileUpload::make('image')
                                            ->label('Imagen')
                                            ->image()
                                            ->directory('operaciones')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9')
                                            ->imageResizeTargetWidth('600')
                                            ->imageResizeTargetHeight('400'),
                                    ])
                                    ->columns(1)
                                    ->collapsible(),
                            ]),
                            Tab::make('Nuestras Operaciones')
                            ->schema([
                                Repeater::make('nuestras_operaciones')
                                    ->label('Galería de Operaciones')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Título')
                                            ->required()
                                            ->placeholder('Ej: Planta de Concreto'),
                                        Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(2)
                                            ->required()
                                            ->placeholder('Ej: Infraestructura moderna para producción eficiente.'),
                                        FileUpload::make('image')
                                            ->label('Imagen')
                                            ->image()
                                            ->directory('operaciones')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9')
                                            ->imageResizeTargetWidth('600')
                                            ->imageResizeTargetHeight('400'),
                                    ])
                                    ->columns(1)
                                    ->collapsible()
                                    ->default([
                                        ['title' => 'Planta de Concreto', 'description' => 'Infraestructura moderna para producción eficiente.'],
                                        ['title' => 'Camión Mixer', 'description' => 'Vehículos especializados para entrega precisa.'],
                                        ['title' => 'Laboratorio Técnico', 'description' => 'Ensayos de calidad y monitoreo de su endurecimiento.'],
                                        ['title' => 'Vaciado en obra', 'description' => 'Aplicación eficiente en campo para grandes estructuras.'],
                                        ['title' => 'Equipo Operativo', 'description' => 'Personal capacitado en todas las fases del proyecto.'],
                                    ]),
                            ]),

                        Tab::make('Cifras')
                            ->schema([
                                Repeater::make('cifras')
                                    ->label('Cifras de la portada')
                                    ->helperText('Se muestran en una franja debajo de la portada, con un conteo animado. Lo ideal son 4.')
                                    ->schema([
                                        TextInput::make('valor')
                                            ->label('Número')
                                            ->required()
                                            ->placeholder('Ej: 15'),
                                        TextInput::make('sufijo')
                                            ->label('Después del número')
                                            ->placeholder('Ej: + o m³/h'),
                                        TextInput::make('etiqueta')
                                            ->label('Texto')
                                            ->required()
                                            ->placeholder('Ej: Mixers en nuestra flota'),
                                    ])
                                    ->columns(3)
                                    ->maxItems(6)
                                    ->reorderable(),
                            ]),

                        Tab::make('Zonas de cobertura')
                            ->schema([
                                TagsInput::make('zonas_cobertura')
                                    ->label('Zonas que atienden')
                                    ->helperText('Escribe cada zona y presiona Enter. Se muestran en la sección "Llegamos a tu obra".')
                                    ->placeholder('Ej: Huancayo'),
                            ]),

                        Tab::make('WhatsApp Flotante')
                            ->schema([
                                TextInput::make('whatsapp_number')
                                    ->label('Número de WhatsApp del botón flotante')
                                    ->placeholder('Ej: 957 924 586')
                                    ->helperText('Aparece en todas las páginas de la web. Puedes escribirlo con espacios; si es un celular peruano se agrega el 51 solo.')
                                    ->dehydrateStateUsing(fn (?string $state) => \App\Models\Inicio::normalizarWhatsapp($state))
                                    ->default('51957924586'),
                            ]),
                        
                        // ===================== LOGO Y MARCA + FAVICON =====================
                        Tab::make('Logo y Marca')
                            ->schema([
                                FileUpload::make('logo_image')
                                    ->label('Logo de la Empresa')
                                    ->image()
                                    ->directory('logo')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->helperText('Sube el logo de la empresa (formato cuadrado recomendado)')
                                    ->columnSpanFull(),
                                
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('logo_text')
                                            ->label('Texto del Logo')
                                            ->maxLength(255)
                                            ->placeholder('Ej: Concretera Mantaro'),
                                        
                                        TextInput::make('logo_text_color')
                                            ->label('Color del Texto Principal')
                                            ->type('color'),
                                        
                                        TextInput::make('logo_span_color')
                                            ->label('Color del Span (Primera palabra)')
                                            ->type('color'),
                                    ]),
                                
                                // ========== FAVICON ==========
                                Section::make('Favicon (Icono de la página)')
                                    ->schema([
                                        FileUpload::make('favicon')
                                            ->label('Icono de la página (favicon)')
                                            ->image()
                                            ->directory('favicon')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('1:1')
                                            ->imageResizeTargetWidth('64')
                                            ->imageResizeTargetHeight('64')
                                            ->helperText('Sube un icono PNG o ICO de 64x64 píxeles. Aparecerá en la pestaña del navegador.')
                                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/vnd.microsoft.icon'])
                                            ->maxSize(100)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Oficina Administrativa')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('oficina_titulo')
                                            ->label('Título')
                                            ->placeholder('Ej: Oficina Administrativa'),
                                        
                                        TextInput::make('oficina_direccion')
                                            ->label('Dirección')
                                            ->placeholder('Ej: Pasaje Miraflores, 12003 – Huancayo'),
                                        
                                        TextInput::make('oficina_telefono')
                                            ->label('Teléfono')
                                            ->placeholder('Ej: 064 762805'),
                                        
                                        TextInput::make('oficina_celular')
                                            ->label('Celular')
                                            ->placeholder('Ej: 925 091 695'),
                                        
                                        TextInput::make('oficina_email')
                                            ->label('Email')
                                            ->email()
                                            ->placeholder('Ej: amgeotop.sac@gmail.com'),
                                        
                                        TextInput::make('oficina_maps_url')
                                            ->label('URL de Google Maps')
                                            ->url()
                                            ->placeholder('https://www.google.com/maps?q=...'),
                                    ]),
                            ]),

                        Tab::make('Planta de Producción')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('planta_titulo')
                                            ->label('Título')
                                            ->placeholder('Ej: Planta de Producción'),
                                        
                                        TextInput::make('planta_ubicacion')
                                            ->label('Ubicación')
                                            ->placeholder('Ej: WQ38+W57, Huamancaca 12425'),
                                        
                                        TextInput::make('planta_maps_url')
                                            ->label('URL de Google Maps')
                                            ->url()
                                            ->placeholder('https://www.google.com/maps?q=...'),
                                        
                                        TextInput::make('planta_whatsapp')
                                            ->label('WhatsApp')
                                            ->placeholder('Ej: +51 982 337 770'),
                                    ]),
                            ]),

                        Tab::make('Atención al Cliente')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('atencion_titulo')
                                            ->label('Título')
                                            ->placeholder('Ej: Atención al Cliente'),
                                        
                                        TextInput::make('atencion_horario_lunes_sabado')
                                            ->label('Horario Lunes a Sábado')
                                            ->placeholder('Ej: 8:00 a.m. – 6:00 p.m.'),
                                        
                                        TextInput::make('atencion_horario_domingo')
                                            ->label('Horario Domingo')
                                            ->placeholder('Ej: Atención previa coordinación'),
                                        
                                        TextInput::make('atencion_asesora_comercial')
                                            ->label('Asesora Comercial')
                                            ->placeholder('Ej: 982 337 770'),
                                    ]),
                            ]),

                        Tab::make('Redes Sociales')
                            ->schema([
                                Repeater::make('social_networks')
                                    ->label('Redes Sociales')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->placeholder('Ej: Facebook, Instagram, TikTok'),
                                        TextInput::make('icon')
                                            ->label('Icono (Bootstrap)')
                                            ->required()
                                            ->placeholder('Ej: bi-facebook, bi-instagram, bi-tiktok')
                                            ->helperText('Ver iconos en: https://icons.getbootstrap.com/'),
                                        TextInput::make('url')
                                            ->label('URL')
                                            ->required()
                                            ->url()
                                            ->placeholder('https://www.facebook.com/tu-pagina'),
                                    ])
                                    ->columns(3)
                                    ->collapsible(),
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
                TextColumn::make('hero_title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInicios::route('/'),
            'create' => CreateInicio::route('/create'),
            'edit' => EditInicio::route('/{record}/edit'),
        ];
    }
}