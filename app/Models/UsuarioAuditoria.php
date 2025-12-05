<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioAuditoria extends Model
{
    protected $table = 'raz_usuarios_auditorias';

    protected $fillable = [
        'dni',
        'password',
        'activo',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Obtener datos del empleado desde pri.empleados
     */
    public function getEmpleadoAttribute()
    {
        return \DB::table('pri.empleados')
            ->where('DNI', $this->dni)
            ->first();
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
