<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class Login extends Component
{
    public $dni = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'dni' => 'required|string',
        'password' => 'required|string',
    ];

    protected $messages = [
        'dni.required' => 'El DNI es obligatorio',
        'password.required' => 'La contraseña es obligatoria',
    ];

    public function login()
    {
        $this->validate();

        // Buscar usuario en raz_usuarios_auditorias
        $usuario = \App\Models\UsuarioAuditoria::where('dni', $this->dni)
            ->where('activo', true)
            ->first();

        if (!$usuario) {
            $this->addError('dni', 'DNI no encontrado o usuario inactivo');
            return;
        }

        // Verificar contraseña
        if (!\Hash::check($this->password, $usuario->password)) {
            $this->addError('password', 'Contraseña incorrecta');
            return;
        }

        // Obtener datos del empleado desde pri.empleados
        $empleado = \DB::table('pri.empleados')
            ->where('DNI', $this->dni)
            ->first();

        if (!$empleado) {
            $this->addError('dni', 'Empleado no encontrado en el sistema');
            return;
        }

        // Guardar en sesión
        $nombreCompleto = trim(
            ($empleado->Nombres ?? '') . ' ' .
            ($empleado->ApellidoPaterno ?? '') . ' ' .
            ($empleado->ApellidoMaterno ?? '')
        );

        session([
            'usuario_id' => $usuario->id,
            'dni' => $empleado->DNI,
            'nombre' => $nombreCompleto ?: 'Usuario',
            'cargoid' => $empleado->CargoID ?? null,
            'autenticado' => true,
        ]);

        // Redireccionar al dashboard
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.guest');
    }
}
