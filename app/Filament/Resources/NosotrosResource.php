<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NosotrosResource\Pages;
use App\Models\Nosotros;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Grid;

class NosotrosResource extends Resource
{
    protected static ?string $model = Nosotros::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Gestión de Contenido';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Nosotros';
    protected static ?string $modelLabel = 'Nosotros';
    protected static ?string $pluralModelLabel = 'Nosotros';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Configuración de Nosotros')
                    ->tabs([
                        // ===================== QUIÉNES SOMOS =====================
                        Tabs\Tab::make('Quiénes Somos')
                            ->schema([
                                Forms\Components\TextInput::make('quienes_somos_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Quiénes Somos')
                                    ->maxLength(255),
                                
                                Forms\Components\RichEditor::make('quienes_somos_text')
                                    ->label('Texto')
                                    ->required()
                                    ->placeholder('Describe quiénes son...')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                                
                                Forms\Components\FileUpload::make('quienes_somos_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),
                        
                        // ===================== MISIÓN =====================
                        Tabs\Tab::make('Misión')
                            ->schema([
                                Forms\Components\TextInput::make('mision_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Misión')
                                    ->maxLength(255),
                                
                                Forms\Components\RichEditor::make('mision_text')
                                    ->label('Texto')
                                    ->required()
                                    ->placeholder('Describe la misión...')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                                
                                Forms\Components\FileUpload::make('mision_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),
                        
                        // ===================== VISIÓN =====================
                        Tabs\Tab::make('Visión')
                            ->schema([
                                Forms\Components\TextInput::make('vision_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Visión')
                                    ->maxLength(255),
                                
                                Forms\Components\RichEditor::make('vision_text')
                                    ->label('Texto')
                                    ->required()
                                    ->placeholder('Describe la visión...')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                                
                                Forms\Components\FileUpload::make('vision_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),
                        
                        // ===================== VALORES =====================
                        Tabs\Tab::make('Valores')
                            ->schema([
                                Forms\Components\TextInput::make('valores_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Nuestros Valores')
                                    ->maxLength(255),
                                
                                Forms\Components\FileUpload::make('valores_image')
                                    ->label('Imagen General')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                                
                                Repeater::make('valores_list')
                                    ->label('Lista de Valores')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Valor')
                                            ->required()
                                            ->placeholder('Ej: Responsabilidad'),
                                        Forms\Components\Textarea::make('description')
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
                        Tabs\Tab::make('Actividad Principal')
                            ->schema([
                                Forms\Components\TextInput::make('actividad_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Actividad Principal')
                                    ->maxLength(255),
                                
                                Forms\Components\RichEditor::make('actividad_text')
                                    ->label('Texto')
                                    ->required()
                                    ->placeholder('Describe la actividad principal...')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                                
                                Forms\Components\FileUpload::make('actividad_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),
                        
                        // ===================== ORGANIGRAMA =====================
                        Tabs\Tab::make('Organigrama')
                            ->schema([
                                Forms\Components\TextInput::make('organigrama_title')
                                    ->label('Título')
                                    ->required()
                                    ->default('Nuestro Organigrama')
                                    ->maxLength(255),
                                
                                Forms\Components\RichEditor::make('organigrama_text')
                                    ->label('Texto')
                                    ->required()
                                    ->placeholder('Describe el organigrama...')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                                
                                Forms\Components\FileUpload::make('organigrama_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('nosotros')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ]),
                        
                        // ===================== ACTIVAR =====================
                        Tabs\Tab::make('Activar')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('quienes_somos_title')
                    ->label('Título')
                    ->searchable()
                    ->limit(30),
                
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNosotros::route('/'),
            'create' => Pages\CreateNosotros::route('/create'),
            'edit' => Pages\EditNosotros::route('/{record}/edit'),
        ];
    }
}