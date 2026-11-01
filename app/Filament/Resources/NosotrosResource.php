<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\NosotrosResource\Pages\ListNosotros;
use App\Filament\Resources\NosotrosResource\Pages\CreateNosotros;
use App\Filament\Resources\NosotrosResource\Pages\EditNosotros;
use App\Filament\Resources\NosotrosResource\Pages;
use App\Models\Nosotros;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;

class NosotrosResource extends Resource
{
    protected static ?string $model = Nosotros::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Contenido';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Nosotros';
    protected static ?string $modelLabel = 'Nosotros';
    protected static ?string $pluralModelLabel = 'Nosotros';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'nosotros';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Configuración de Nosotros')
                    ->tabs([
                        // ===================== QUIÉNES SOMOS =====================
                        Tab::make('Quiénes Somos')
                            ->schema([
                                TextInput::make('quienes_somos_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Quiénes Somos')
                                    ->maxLength(255),
                                
                                RichEditor::make('quienes_somos_text')
                                    ->label('Texto')
                                    ->required()
                                    ->placeholder('Describe quiénes son...')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                                
                                FileUpload::make('quienes_somos_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),
                        
                        // ===================== MISIÓN =====================
                        Tab::make('Misión')
                            ->schema([
                                TextInput::make('mision_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Misión')
                                    ->maxLength(255),
                                
                                RichEditor::make('mision_text')
                                    ->label('Texto')
                                    ->required()
                                    ->placeholder('Describe la misión...')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                                
                                FileUpload::make('mision_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),
                        
                        // ===================== VISIÓN =====================
                        Tab::make('Visión')
                            ->schema([
                                TextInput::make('vision_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Visión')
                                    ->maxLength(255),
                                
                                RichEditor::make('vision_text')
                                    ->label('Texto')
                                    ->required()
                                    ->placeholder('Describe la visión...')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                                
                                FileUpload::make('vision_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),
                        
                        // ===================== VALORES =====================
                        Tab::make('Valores')
                            ->schema([
                                TextInput::make('valores_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Nuestros Valores')
                                    ->maxLength(255),
                                
                                FileUpload::make('valores_image')
                                    ->label('Imagen General')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                                
                                Repeater::make('valores_list')
                                    ->label('Lista de Valores')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Valor')
                                            ->required()
                                            ->placeholder('Ej: Responsabilidad'),
                                        Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(2)
                                            ->placeholder('Describe este valor...'),
                                    ])
                                    ->columns(1)
                                    ->collapsible()
                                    ->default([
                                        ['title' => 'Responsabilidad', 'description' => 'Compromiso con nuestros clientes y la sociedad.'],
                                        ['title' => 'Calidad', 'description' => 'Excelencia en cada proyecto.'],
                                        ['title' => 'Innovación', 'description' => 'Tecnología de vanguardia.'],
                                    ]),
                            ]),
                        
                        // ===================== ACTIVIDAD PRINCIPAL =====================
                        Tab::make('Actividad Principal')
                            ->schema([
                                TextInput::make('actividad_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Actividad Principal')
                                    ->maxLength(255),
                                
                                RichEditor::make('actividad_text')
                                    ->label('Texto')
                                    ->required()
                                    ->placeholder('Describe la actividad principal...')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                                
                                FileUpload::make('actividad_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),
                        
                        // ===================== ORGANIGRAMA =====================
                        Tab::make('Organigrama')
                            ->schema([
                                TextInput::make('organigrama_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Nuestro Organigrama')
                                    ->maxLength(255),
                                
                                RichEditor::make('organigrama_text')
                                    ->label('Texto')
                                    ->required()
                                    ->placeholder('Describe el organigrama...')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                                
                                FileUpload::make('organigrama_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),
                        
                        // ===================== ACTIVAR =====================
                        Tab::make('Activar')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Solo un registro puede estar activo a la vez'),
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
                TextColumn::make('quienes_somos_title')
                    ->label('Título')
                    ->searchable()
                    ->limit(30),
                
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNosotros::route('/'),
            'create' => CreateNosotros::route('/create'),
            'edit' => EditNosotros::route('/{record}/edit'),
        ];
    }
}