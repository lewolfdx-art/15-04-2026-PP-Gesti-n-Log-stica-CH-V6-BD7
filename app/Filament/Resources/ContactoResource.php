<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use App\Filament\Resources\ContactoResource\Pages\ListContactos;
use App\Filament\Resources\ContactoResource\Pages\CreateContacto;
use App\Filament\Resources\ContactoResource\Pages\EditContacto;
use App\Filament\Resources\ContactoResource\Pages;
use App\Models\Contacto;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactoResource extends Resource
{
    protected static ?string $model = Contacto::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Contenido';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Mensajes de Contacto';
    protected static ?string $modelLabel = 'Mensaje';
    protected static ?string $pluralModelLabel = 'Mensajes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Andrés Rodríguez'),
                        
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->placeholder('Ej: andres@correo.com'),
                    ]),
                
                Grid::make(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->maxLength(255)
                            ->placeholder('Ej: 925 091 695'),
                        
                        TextInput::make('subject')
                            ->label('Asunto')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Cotización de concreto'),
                    ]),
                
                Textarea::make('message')
                    ->label('Mensaje')
                    ->required()
                    ->rows(5)
                    ->placeholder('Escribe tu mensaje aquí...')
                    ->columnSpanFull(),
                
                Toggle::make('is_read')
                    ->label('Marcar como leído')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                
                TextColumn::make('subject')
                    ->label('Asunto')
                    ->searchable()
                    ->limit(30),
                
                IconColumn::make('is_read')
                    ->label('Leído')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_read')
                    ->label('Leído'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('marcar_leido')
                        ->label('Marcar como leídos')
                        ->action(fn ($records) => $records->each->update(['is_read' => true]))
                        ->icon('heroicon-o-check-circle')
                        ->color('success'),
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
            'index' => ListContactos::route('/'),
            'create' => CreateContacto::route('/create'),
            'edit' => EditContacto::route('/{record}/edit'),
            
        ];
    }
}