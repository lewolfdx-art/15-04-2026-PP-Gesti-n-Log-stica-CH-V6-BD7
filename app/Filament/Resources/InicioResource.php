<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InicioResource\Pages;
use App\Models\Inicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;

class InicioResource extends Resource
{
    protected static ?string $model = Inicio::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationGroup = 'Gestión de Contenido';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Configuración de Inicio')
                    ->tabs([
                        Tabs\Tab::make('Hero Principal')
                            ->schema([
                                Forms\Components\TextInput::make('hero_title')
                                    ->label('Título Principal')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Concreto Premezclado de Alta Calidad')
                                    ->columnSpanFull(),
                                
                                Forms\Components\Textarea::make('hero_description')
                                    ->label('Descripción del Hero')
                                    ->rows(3)
                                    ->required()
                                    ->placeholder('Describe los servicios y beneficios principales de la empresa...')
                                    ->columnSpanFull(),
                                
                                Forms\Components\FileUpload::make('hero_image')
                                    ->label('Imagen del Hero')
                                    ->image()
                                    ->directory('inicio')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->columnSpanFull(),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Solo un registro puede estar activo a la vez'),
                            ])
                            ->columns(2),
                        
                        Tabs\Tab::make('Botón CTA')
                            ->schema([
                                Forms\Components\TextInput::make('cta_text')
                                    ->label('Texto del Botón')
                                    ->maxLength(255)
                                    ->placeholder('Ej: Cotizar Ahora, Solicitar Información')
                                    ->columnSpanFull(),
                                
                                Forms\Components\TextInput::make('cta_url')
                                    ->label('URL del Botón')
                                    ->maxLength(255)
                                    ->placeholder('Ej: /contacto, /cotizar')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                        
                        Tabs\Tab::make('Beneficios')
                            ->schema([
                                Repeater::make('beneficios')
                                    ->label('Lista de Beneficios')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título')
                                            ->required()
                                            ->placeholder('Ej: Plantas Automatizadas'),
                                        Forms\Components\TextInput::make('icon')
                                            ->label('Icono (Bootstrap)')
                                            ->placeholder('Ej: bi-gear-fill, bi-check-circle-fill')
                                            ->helperText('Ver iconos en: https://icons.getbootstrap.com/'),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(2)
                                            ->required()
                                            ->placeholder('Describe el beneficio detalladamente...'),
                                    ])
                                    ->columns(1)
                                    ->collapsible(),
                            ]),
                        
                        Tabs\Tab::make('Proyectos Recientes')
                            ->schema([
                                Repeater::make('proyectos_recientes')
                                    ->label('Lista de Proyectos')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título del Proyecto')
                                            ->required()
                                            ->placeholder('Ej: Centro de Salud Militar N°31'),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(2)
                                            ->required()
                                            ->placeholder('Ej: Huancayo – Obra hospitalaria'),
                                            Forms\Components\FileUpload::make('image')
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
                            Tabs\Tab::make('Nuestras Operaciones')
                            ->schema([
                                Repeater::make('nuestras_operaciones')
                                    ->label('Galería de Operaciones')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título')
                                            ->required()
                                            ->placeholder('Ej: Planta de Concreto'),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(2)
                                            ->required()
                                            ->placeholder('Ej: Infraestructura moderna para producción eficiente.'),
                                        Forms\Components\FileUpload::make('image')
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
                        
                        Tabs\Tab::make('WhatsApp Flotante')
                            ->schema([
                                Forms\Components\TextInput::make('whatsapp_number')
                                    ->label('Número de WhatsApp (con código de país)')
                                    ->placeholder('Ej: 51982337770')
                                    ->helperText('Formato: código de país + número sin símbolos. Ej: 51982337770')
                                    ->default('51982337770'),
                            ]),
                        Tabs\Tab::make('Logo y Marca')
                            ->schema([
                                Forms\Components\FileUpload::make('logo_image')
                                    ->label('Logo de la Empresa')
                                    ->image()
                                    ->directory('logo')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->helperText('Sube el logo de la empresa (formato cuadrado recomendado)')
                                    ->columnSpanFull(),
                                
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('logo_text')
                                            ->label('Texto del Logo')
                                            ->maxLength(255)
                                            ->placeholder('Ej: Concretera Mantaro'),
                                        
                                        Forms\Components\TextInput::make('logo_text_color')
                                            ->label('Color del Texto Principal')
                                            ->type('color'),
                                        
                                        Forms\Components\TextInput::make('logo_span_color')
                                            ->label('Color del Span (Primera palabra)')
                                            ->type('color'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Oficina Administrativa')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('oficina_titulo')
                                            ->label('Título')
                                            ->placeholder('Ej: Oficina Administrativa'),
                                        
                                        Forms\Components\TextInput::make('oficina_direccion')
                                            ->label('Dirección')
                                            ->placeholder('Ej: Pasaje Miraflores, 12003 – Huancayo'),
                                        
                                        Forms\Components\TextInput::make('oficina_telefono')
                                            ->label('Teléfono')
                                            ->placeholder('Ej: 064 762805'),
                                        
                                        Forms\Components\TextInput::make('oficina_celular')
                                            ->label('Celular')
                                            ->placeholder('Ej: 925 091 695'),
                                        
                                        Forms\Components\TextInput::make('oficina_email')
                                            ->label('Email')
                                            ->email()
                                            ->placeholder('Ej: amgeotop.sac@gmail.com'),
                                        
                                        Forms\Components\TextInput::make('oficina_maps_url')
                                            ->label('URL de Google Maps')
                                            ->url()
                                            ->placeholder('https://www.google.com/maps?q=...'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Planta de Producción')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('planta_titulo')
                                            ->label('Título')
                                            ->placeholder('Ej: Planta de Producción'),
                                        
                                        Forms\Components\TextInput::make('planta_ubicacion')
                                            ->label('Ubicación')
                                            ->placeholder('Ej: WQ38+W57, Huamancaca 12425'),
                                        
                                        Forms\Components\TextInput::make('planta_maps_url')
                                            ->label('URL de Google Maps')
                                            ->url()
                                            ->placeholder('https://www.google.com/maps?q=...'),
                                        
                                        Forms\Components\TextInput::make('planta_whatsapp')
                                            ->label('WhatsApp')
                                            ->placeholder('Ej: +51 982 337 770'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Atención al Cliente')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('atencion_titulo')
                                            ->label('Título')
                                            ->placeholder('Ej: Atención al Cliente'),
                                        
                                        Forms\Components\TextInput::make('atencion_horario_lunes_sabado')
                                            ->label('Horario Lunes a Sábado')
                                            ->placeholder('Ej: 8:00 a.m. – 6:00 p.m.'),
                                        
                                        Forms\Components\TextInput::make('atencion_horario_domingo')
                                            ->label('Horario Domingo')
                                            ->placeholder('Ej: Atención previa coordinación'),
                                        
                                        Forms\Components\TextInput::make('atencion_asesora_comercial')
                                            ->label('Asesora Comercial')
                                            ->placeholder('Ej: 982 337 770'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Redes Sociales')
                            ->schema([
                                Repeater::make('social_networks')
                                    ->label('Redes Sociales')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->placeholder('Ej: Facebook, Instagram, TikTok'),
                                        Forms\Components\TextInput::make('icon')
                                            ->label('Icono (Bootstrap)')
                                            ->required()
                                            ->placeholder('Ej: bi-facebook, bi-instagram, bi-tiktok')
                                            ->helperText('Ver iconos en: https://icons.getbootstrap.com/'),
                                        Forms\Components\TextInput::make('url')
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
                Tables\Columns\TextColumn::make('hero_title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInicios::route('/'),
            'create' => Pages\CreateInicio::route('/create'),
            'edit' => Pages\EditInicio::route('/{record}/edit'),
        ];
    }
}