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
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $username
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin withoutRole($roles, $guard = null)
 */
	class Admin extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $solicitud_id
 * @property string $titulo
 * @property string $nombre
 * @property string $tipo_id
 * @property string $numero_id
 * @property string $favorable
 * @property string|null $concepto_no_favorable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Solicitud $solicitud
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro whereConceptoNoFavorable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro whereFavorable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro whereNumeroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro whereSolicitudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro whereTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro whereTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Miembro whereUpdatedAt($value)
 */
	class Miembro extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $solicitud_id
 * @property string|null $estado_anterior
 * @property string $estado_nuevo
 * @property string|null $comentario
 * @property string $fecha_movimiento
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Solicitud $solicitud
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud whereComentario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud whereEstadoAnterior($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud whereEstadoNuevo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud whereFechaMovimiento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud whereSolicitudId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovimientoSolicitud whereUpdatedAt($value)
 */
	class MovimientoSolicitud extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $tipo_persona
 * @property \Illuminate\Support\Carbon $fecha_registro
 * @property string $razon_social
 * @property string $tipo_id
 * @property string $identificador
 * @property string $motivo
 * @property string|null $nombre_completo
 * @property string|null $tipo_cliente
 * @property string $estado
 * @property string|null $tipo_visitante
 * @property array<array-key, mixed>|null $archivo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $admin_id
 * @property string|null $concepto
 * @property string|null $concepto_sagrilaft
 * @property string|null $concepto_ptee
 * @property string|null $motivo_rechazo
 * @property-read \App\Models\Admin|null $admin
 * @property-read mixed $archivos
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Miembro> $miembros
 * @property-read int|null $miembros_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereArchivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereConcepto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereConceptoPtee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereConceptoSagrilaft($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereFechaRegistro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereIdentificador($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereMotivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereMotivoRechazo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereRazonSocial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereTipoCliente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereTipoPersona($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereTipoVisitante($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Solicitud whereUpdatedAt($value)
 */
	class Solicitud extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $identificador
 * @property string $tipo
 * @property string $nombre_completo
 * @property string $empresa
 * @property string $fecha_registro
 * @property string $fecha_vigencia
 * @property string $cargo
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information whereCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information whereEmpresa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information whereFechaRegistro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information whereFechaVigencia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information whereIdentificador($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|information whereUpdatedAt($value)
 */
	class information extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

