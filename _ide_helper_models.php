<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $fecha
 * @property string|null $ubicacion_referencia
 * @property string|null $estructura
 * @property string|null $nombre_cliente
 * @property string|null $nombre_vendedor
 * @property string|null $numero_contrato
 * @property string|null $tipo_concreto
 * @property string|null $guia_remision
 * @property string|null $factura
 * @property string|null $bombeo
 * @property numeric $bomba
 * @property numeric $bomba_adicional
 * @property numeric $volumen_guia
 * @property numeric $volumen_real
 * @property numeric $volumen_sobrante
 * @property numeric|null $pu
 * @property numeric|null $monto_total
 * @property numeric $descuento_guias
 * @property numeric|null $venta_neta
 * @property numeric|null $pu_real
 * @property numeric $gastos_alimentos_bomba
 * @property numeric $descuentos
 * @property numeric $alimentos_dc
 * @property string $estado_pago
 * @property string|null $forma_pago
 * @property string|null $cancelacion
 * @property string|null $observacion
 * @property string|null $observaciones_adicionales
 * @property numeric $comision
 * @property numeric $comision_igv
 * @property numeric|null $total_para_empresa
 * @property int|null $cliente_id
 * @property int|null $vendedor_id
 * @property int|null $gasto_categoria_id
 * @property bool $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereAlimentosDc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereBomba($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereBombaAdicional($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereBombeo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereCancelacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereClienteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereComision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereComisionIgv($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereDescuentoGuias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereDescuentos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereEstadoPago($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereEstructura($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereFactura($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereFormaPago($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereGastoCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereGastosAlimentosBomba($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereGuiaRemision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereMontoTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereNombreCliente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereNombreVendedor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereNumeroContrato($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereObservacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereObservacionesAdicionales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato wherePu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato wherePuReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereTipoConcreto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereTotalParaEmpresa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereUbicacionReferencia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereVendedorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereVentaNeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereVolumenGuia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereVolumenReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contrato whereVolumenSobrante($value)
 */
	class Contrato extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $detalle
 * @property string $tipo
 * @property \Illuminate\Support\Carbon|null $fecha
 * @property string|null $mes
 * @property string|null $año
 * @property string|null $cantidad
 * @property numeric $total
 * @property bool $pagado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar whereAño($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar whereCantidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar whereDetalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar whereMes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar wherePagado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CuentaPorPagar whereUpdatedAt($value)
 */
	class CuentaPorPagar extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $tipo
 * @property string $valor
 * @property string|null $descripcion
 * @property int $orden
 * @property bool $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoOperacion whereValor($value)
 */
	class DatoOperacion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $fecha
 * @property string|null $grupo
 * @property int|null $gasto_categoria_id
 * @property string|null $subcategoria
 * @property string|null $responsable
 * @property string $detalle
 * @property numeric $importe
 * @property string|null $obs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\GastoCategoria|null $categoria
 * @property-read mixed $categoria_completa
 * @property-read mixed $importe_formateado
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto delMes($mes, $año = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto porCategoria($categoriaId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto porFecha($fecha)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereDetalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereGastoCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereGrupo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereImporte($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereObs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereResponsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereSubcategoria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gasto withoutTrashed()
 */
	class Gasto extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string $grupo
 * @property int|null $parent_id
 * @property int $orden
 * @property int $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, GastoCategoria> $children
 * @property-read int|null $children_count
 * @property-read GastoCategoria|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria whereGrupo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GastoCategoria whereUpdatedAt($value)
 */
	class GastoCategoria extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $fecha
 * @property string $modo_pago
 * @property string|null $numero_contrato
 * @property string|null $asesor
 * @property string|null $detalle
 * @property numeric $monto
 * @property string|null $banco
 * @property string|null $obs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereAsesor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereBanco($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereDetalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereModoPago($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereMonto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereNumeroContrato($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereObs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ingreso whereUpdatedAt($value)
 */
	class Ingreso extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $tipo_periodo
 * @property int|null $semana
 * @property \Illuminate\Support\Carbon|null $fecha_desde
 * @property \Illuminate\Support\Carbon|null $fecha_hasta
 * @property string|null $quincena
 * @property int|null $mes
 * @property int|null $año
 * @property \Illuminate\Support\Carbon|null $fecha_desde_quincena
 * @property \Illuminate\Support\Carbon|null $fecha_hasta_quincena
 * @property \Illuminate\Support\Carbon|null $fecha_fin_mes
 * @property string $proveedor
 * @property string $detalle
 * @property numeric $monto
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereAño($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereDetalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereFechaDesde($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereFechaDesdeQuincena($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereFechaFinMes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereFechaHasta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereFechaHastaQuincena($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereMes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereMonto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereProveedor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereQuincena($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereSemana($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereTipoPeriodo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteSemanal whereUpdatedAt($value)
 */
	class ReporteSemanal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $tipo_cargo
 * @property string $nombre_completo
 * @property string|null $dni
 * @property \Illuminate\Support\Carbon|null $fecha_nacimiento
 * @property string|null $descripcion
 * @property int $orden
 * @property bool $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereDni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereFechaNacimiento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereTipoCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereUpdatedAt($value)
 */
	class Trabajador extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser {}
}

