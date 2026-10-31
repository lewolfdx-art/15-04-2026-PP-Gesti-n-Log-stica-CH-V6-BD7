<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\TrabajadoresResource\Pages\ListTrabajadores;
use App\Filament\Resources\TrabajadoresResource\Pages\CreateTrabajadores;
use App\Filament\Resources\TrabajadoresResource\Pages\EditTrabajadores;
use App\Filament\Resources\TrabajadoresResource\Pages;
use App\Models\Trabajador;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrabajadoresResource extends Resource
{
    protected static ?string $model = Trabajador::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Personal';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Trabajadores / Operadores';
    protected static ?string $modelLabel = 'Trabajador';
    protected static ?string $pluralModelLabel = 'Trabajadores / Operadores';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'trabajadores';

    protected static ?string $recordTitleAttribute = 'nombre_completo';

    /**
     * Solo personal: los asesores de ventas se gestionan en AsesorVentasResource
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->personal();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo_cargo')
                    ->label('Cargo / Tipo')
                    ->options(Trabajador::CARGOS_PERSONAL)
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->live(),

                TextInput::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(255),

                TextInput::make('dni')
                    ->label('DNI')
                    ->required()
                    ->mask('99999999')
                    ->placeholder('12345678')
                    ->rule('digits:8')
                    ->unique(ignoreRecord: true),

                // SWITCH PARA HABILITAR/DESHABILITAR FECHA DE NACIMIENTO
                Toggle::make('tiene_fecha_nacimiento')
                    ->label('¿Tiene fecha de nacimiento?')
                    ->default(true)
                    ->live()
                    ->columnSpanFull(),

                DatePicker::make('fecha_nacimiento')
                    ->label('Fecha de Nacimiento')
                    ->default(now())
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->maxDate(now())
                    ->closeOnDateSelection(true)
                    ->placeholder('dd/mm/yyyy')
                    ->required(fn (Get $get) => $get('tiene_fecha_nacimiento') === true)
                    ->disabled(fn (Get $get) => $get('tiene_fecha_nacimiento') === false)
                    ->dehydrated(fn (Get $get) => $get('tiene_fecha_nacimiento') === true)
                    ->rules(fn (Get $get) => $get('tiene_fecha_nacimiento') === true ? ['required', 'date'] : ['nullable', 'date'])
                    ->afterStateHydrated(function ($state, callable $set) {
                        if (empty($state)) {
                            $set('tiene_fecha_nacimiento', false);
                        }
                    }),

                TextInput::make('sueldo_mensual')
                    ->label('Sueldo mensual')
                    ->prefix('S/')
                    ->numeric()
                    ->minValue(0)
                    ->helperText('Se paga por quincenas: cada quincena propone la mitad (en Pagos al personal).'),

                Textarea::make('descripcion')
                    ->label('Descripción / Observaciones')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo_cargo')
                    ->label('Cargo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => Trabajador::cargoTexto($state))
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('nombre_completo')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('dni')
                    ->label('DNI')
                    ->searchable(),

                TextColumn::make('sueldo_mensual')
                    ->label('Sueldo mensual')
                    ->money('PEN')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('user.email')
                    ->label('Acceso al sistema')
                    ->icon('heroicon-o-key')
                    ->placeholder('Sin acceso')
                    ->toggleable(),

                TextColumn::make('fecha_nacimiento')
                    ->label('Fecha Nac.')
                    ->date('d/m/Y'),

                TextColumn::make('descripcion')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('activo'),
            ])
            ->defaultSort('nombre_completo', 'asc')
            ->searchable()
            ->filters([
                SelectFilter::make('tipo_cargo')
                    ->label('Cargo')
                    ->options(Trabajador::CARGOS_PERSONAL)
                    ->multiple()
                    ->searchable(),

                TernaryFilter::make('activo'),
            ])
            ->recordActions([
                // Crea el usuario con rol "operador" para que vea sus obras en /operador
                Action::make('dar_acceso')
                    ->label('Dar acceso')
                    ->icon('heroicon-o-key')
                    ->color('success')
                    ->visible(fn (Trabajador $record): bool => $record->user_id === null)
                    ->modalHeading(fn (Trabajador $record): string => "Acceso al sistema para {$record->nombre_completo}")
                    ->modalDescription('Podrá entrar desde el celular a /operador y ver solo las obras que se le asignen.')
                    ->schema([
                        TextInput::make('email')
                            ->label('Correo (usuario)')
                            ->email()
                            ->required()
                            // Regla directa: unique() de Filament excluiría el id del trabajador de la fila
                            ->rule('unique:users,email'),

                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8),
                    ])
                    ->action(function (Trabajador $record, array $data): void {
                        $usuario = User::create([
                            'name'      => $record->nombre_completo,
                            'email'     => $data['email'],
                            'password'  => $data['password'],
                            'is_active' => true,
                        ]);
                        $usuario->assignRole('operador');
                        $record->update(['user_id' => $usuario->getKey()]);

                        Notification::make()
                            ->title('Acceso creado')
                            ->body("{$record->nombre_completo} ya puede entrar en " . url('/operador'))
                            ->success()
                            ->send();
                    }),
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
            'index'  => ListTrabajadores::route('/'),
            'create' => CreateTrabajadores::route('/create'),
            'edit'   => EditTrabajadores::route('/{record}/edit'),
        ];
    }
}