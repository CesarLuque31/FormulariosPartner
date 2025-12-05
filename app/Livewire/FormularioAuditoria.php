<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class FormularioAuditoria extends Component
{
    public $paso = 0; // Paso 0: Selección manual/aleatorio

    // Paso 0: Modo de selección
    public $idCicManual = '';

    // Filtros opcionales para selección aleatoria
    public $filtroCampana = '';
    public $filtroDni = '';
    public $filtroFecha = '';
    public $campanasDisponibles = [];

    // Control de guardado
    public $guardado = false;
    public $bloquearRandomizar = false;

    // Debug property
    public $debugInput = '';

    // Preguntas dinámicas del Paso 1
    public $preguntasPaso1 = [];

    // Preguntas dinámicas del Paso 2
    public $preguntasPaso2 = [];
    public $respuestasPaso2 = [];

    // Preguntas dinámicas del Paso 3
    public $preguntasPaso3 = [];
    public $respuestasPaso3 = [];

    // Preguntas dinámicas del Paso 4
    public $preguntasPaso4 = [];
    public $respuestasPaso4 = [];

    // Preguntas dinámicas del Paso 5
    public $preguntasPaso5 = [];
    public $respuestasPaso5 = [];

    // Preguntas dinámicas del Paso 6
    public $preguntasPaso6 = [];
    public $respuestasPaso6 = [];

    // Preguntas dinámicas del Paso 7
    public $preguntasPaso7 = [];
    public $respuestasPaso7 = [];

    // Preguntas dinámicas del Paso 8
    public $preguntasPaso8 = [];
    public $respuestasPaso8 = [];

    // Preguntas dinámicas del Paso 9
    public $preguntasPaso9 = [];
    public $respuestasPaso9 = [];

    // Respuestas para Pasos 10, 11, 12 (preguntas se cargan dinámicamente)
    public $respuestasPaso10 = [];
    public $respuestasPaso11 = [];
    public $respuestasPaso12 = [];

    // Paso 1: Datos de llamada
    public $nombreAnalista;
    public $idCic;
    public $telefono;
    public $fechaLlamada;
    public $fechaMonitoreo;
    public $duracionFormato;
    public $nombreAsesor;
    public $usuarioAsesor;
    public $campana;

    // Datos internos
    public $duracionSegundos;
    public $auditoriaId = null;
    public $formularioId = 1;
    public $esHogar = false;

    public function mount()
    {
        \Illuminate\Support\Facades\Log::info('FormularioAuditoria mount started', ['session_dni' => session('dni')]);

        // Obtener nombre del analista desde sesión
        $empleado = DB::table('pri.empleados')
            ->where('DNI', session('dni'))
            ->first();

        \Illuminate\Support\Facades\Log::info('Empleado query result', ['found' => $empleado ? 'yes' : 'no']);

        if ($empleado) {
            $this->nombreAnalista = trim(
                ($empleado->Nombres ?? '') . ' ' .
                ($empleado->ApellidoPaterno ?? '') . ' ' .
                ($empleado->ApellidoMaterno ?? '')
            );
        }

        \Illuminate\Support\Facades\Log::info('FormularioAuditoria mount finished');

        // Fecha de monitoreo = hoy
        $this->fechaMonitoreo = now()->format('Y-m-d');

        // Cargar campañas disponibles para el filtro
        $this->campanasDisponibles = DB::table('dbo.Reporte_Llamadas_Detalle')
            ->where('Fecha', '>=', '2025-10-01')
            ->whereNotNull('Campaña_Agente')
            ->where('Campaña_Agente', '!=', '')
            ->distinct()
            ->pluck('Campaña_Agente')
            ->sort()
            ->values()
            ->toArray();

        // Cargar preguntas del Paso 1 desde la base de datos
        $this->preguntasPaso1 = DB::table('raz_preguntas_auditorias')
            ->where('formulario_id', 1)
            ->where('seccion', 'Datos de la Llamada')
            ->orderBy('orden')
            ->get()
            ->toArray();
    }

    // Watcher para detectar selección de campaña crosselling, prepago digital o hogar
    public function updatedFiltroCampana($value)
    {
        // Si se selecciona una campaña que contenga "crosselling", redirigir
        if (
            !empty($value) && (
                stripos($value, 'crosselling') !== false ||
                stripos($value, 'cross selling') !== false
            )
        ) {
            return redirect()->route('auditoria.crosselling');
        }

        // Si se selecciona una campaña que contenga "prepago digital" o "digital", redirigir
        if (
            !empty($value) && (
                stripos($value, 'prepago digital') !== false ||
                stripos($value, 'digital') !== false
            )
        ) {
            return redirect()->route('auditoria.digital');
        }

        // Si se selecciona una campaña que contenga "hogar", redirigir
        if (
            !empty($value) && (
                stripos($value, 'hogar') !== false
            )
        ) {
            return redirect()->route('auditoria.hogar');
        }
    }

    public function seleccionarAleatorio()
    {
        \Illuminate\Support\Facades\Log::info('seleccionarAleatorio called');
        // Validar fecha si fue ingresada
        if (!empty($this->filtroFecha)) {
            $this->validarFecha();
            if ($this->getErrorBag()->has('filtroFecha')) {
                \Illuminate\Support\Facades\Log::warning('Validation failed for date');
                return;
            }
        }

        $this->cargarLlamadaAleatoria();
        $this->paso = 1;
        \Illuminate\Support\Facades\Log::info('seleccionarAleatorio finished, paso set to 1');
    }

    public function validarFecha()
    {
        $fechaMin = '2025-10-01';
        $fechaMax = date('Y-m-d');

        if ($this->filtroFecha < $fechaMin || $this->filtroFecha > $fechaMax) {
            $this->addError('filtroFecha', 'La fecha debe estar entre Octubre 2025 y hoy.');
        }
    }

    public function seleccionarManual()
    {
        $this->validate([
            'idCicManual' => 'required|string',
        ], [
            'idCicManual.required' => 'Debes ingresar un ID CIC',
        ]);

        // Verificar si el CIC ya fue auditado
        $yaAuditado = DB::table('raz_auditorias_calidad')
            ->whereRaw("JSON_VALUE(metadata, '$.id_cic') = ?", [$this->idCicManual])
            ->exists();

        if ($yaAuditado) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => 'CIC ya auditado',
                'text' => 'Este CIC ya fue auditado anteriormente. No se puede auditar de nuevo.'
            ]);
            return;
        }

        $this->cargarLlamadaPorId($this->idCicManual);
        $this->paso = 1;
    }

    public function cargarLlamadaAleatoria()
    {
        // Obtener IDs ya auditados usando JSON_VALUE
        $idsAuditados = DB::table('raz_auditorias_calidad')
            ->whereNotNull('metadata')
            ->get()
            ->pluck('metadata')
            ->map(function ($metadata) {
                $data = json_decode($metadata, true);
                return $data['id_cic'] ?? null;
            })
            ->filter()
            ->toArray();

        // Query para llamada aleatoria con filtros opcionales
        $query = DB::table('dbo.Reporte_Llamadas_Detalle')
            ->where('Fecha', '>=', '2025-10-01')
            ->where('Duracion', '>=', 30);

        // Filtro por campaña (opcional)
        if (!empty($this->filtroCampana)) {
            $query->where('Campaña_Agente', $this->filtroCampana);
        }

        // Filtro por DNI (opcional)
        if (!empty($this->filtroDni)) {
            $query->where('DNI_Empleado', $this->filtroDni);
        }

        // Filtro por fecha específica (opcional)
        if (!empty($this->filtroFecha)) {
            $query->whereDate('Fecha', $this->filtroFecha);
        }

        // Excluir llamadas de Crosselling, Prepago Digital y Hogar (tienen sus propios formularios)
        $query->where('Campaña_Agente', 'NOT LIKE', '%crosselling%')
            ->where('Campaña_Agente', 'NOT LIKE', '%cross selling%')
            ->where('Campaña_Agente', 'NOT LIKE', '%Crosselling%')
            ->where('Campaña_Agente', 'NOT LIKE', '%Cross Selling%')
            ->where('Campaña_Agente', 'NOT LIKE', '%Prepago Digital%')
            ->where('Campaña_Agente', 'NOT LIKE', '%prepago digital%')
            ->where('Campaña_Agente', 'NOT LIKE', '%PREPAGO DIGITAL%')
            ->where('Campaña_Agente', 'NOT LIKE', '%Hogar%')
            ->where('Campaña_Agente', 'NOT LIKE', '%hogar%')
            ->where('Campaña_Agente', 'NOT LIKE', '%HOGAR%');

        // Excluir IDs ya auditados
        if (!empty($idsAuditados)) {
            $query->whereNotIn('ID_Largo', $idsAuditados);
        }

        \Illuminate\Support\Facades\Log::info('Executing random call query');
        $llamada = $query->inRandomOrder()->first();
        \Illuminate\Support\Facades\Log::info('Query result', ['found' => $llamada ? 'yes' : 'no']);

        if ($llamada) {
            // $this->nombreAnalista ya se estableció en mount() con el nombre completo
            $this->idCic = $llamada->ID_Largo;
            $this->telefono = $llamada->Numero;
            $this->fechaLlamada = $llamada->Fecha;
            $this->fechaMonitoreo = date('Y-m-d'); // Fecha actual
            $this->duracionSegundos = $llamada->Duracion;
            $this->duracionFormato = $this->formatDuration($llamada->Duracion);
            $this->nombreAsesor = $llamada->NombreCompletoAgente;
            $this->usuarioAsesor = $llamada->Usuario_Llamada_Origen;
            $this->campana = $llamada->Campaña_Agente ?? 'N/A';

            // Usar formulario estándar (ID 1) para todas las campañas
            $this->formularioId = 1;

            // Recargar preguntas del Paso 1
            $this->preguntasPaso1 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', $this->formularioId)
                ->where('seccion', 'Datos de la Llamada')
                ->orderBy('orden')
                ->get()
                ->toArray();

        } else {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => 'Sin resultados',
                'text' => 'No hay llamadas disponibles con los filtros seleccionados'
            ]);
        }
    }

    public function randomizar()
    {
        $this->cargarLlamadaAleatoria();

        if ($this->idCic) {
            $this->dispatch('showAlert', [
                'type' => 'success',
                'title' => 'Nueva llamada cargada',
                'text' => "ID CIC: {$this->idCic}"
            ]);
        }
    }

    public function cargarLlamadaPorId($idCic)
    {
        $llamada = DB::table('dbo.Reporte_Llamadas_Detalle')
            ->where('ID_Largo', $idCic)
            ->first();

        if ($llamada) {
            $this->idCic = $llamada->ID_Largo;
            $this->telefono = $llamada->Numero;
            $this->fechaLlamada = $llamada->Fecha;
            $this->duracionSegundos = $llamada->Duracion;
            $this->duracionFormato = $this->formatDuration($llamada->Duracion);
            $this->nombreAsesor = $llamada->NombreCompletoAgente;
            $this->usuarioAsesor = $llamada->Usuario_Llamada_Origen;
            $this->campana = $llamada->Campaña_Agente ?? 'N/A';

            // Detectar si es campaña Crosselling y redirigir
            if (stripos($this->campana, 'Crosselling') !== false || stripos($this->campana, 'Cross') !== false) {
                // Redirigir a formulario de Crosselling con el ID como query parameter
                return redirect()->route('auditoria.crosselling')->with('idCicManual', $this->idCic);
            }

            // Detectar si es campaña Prepago Digital y redirigir
            if (stripos($this->campana, 'Prepago Digital') !== false || stripos($this->campana, 'Digital') !== false) {
                // Redirigir a formulario de Prepago Digital con el ID como query parameter
                return redirect()->route('auditoria.digital')->with('idCicManual', $this->idCic);
            }

            // Detectar si es campaña Hogar y redirigir
            if (stripos($this->campana, 'Hogar') !== false) {
                // Redirigir a formulario de Hogar con el ID como query parameter
                return redirect()->route('auditoria.hogar')->with('idCicManual', $this->idCic);
            }

            // Usar formulario estándar (ID 1) para todas las campañas
            $this->formularioId = 1;

            // Recargar preguntas del Paso 1
            $this->preguntasPaso1 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', $this->formularioId)
                ->where('seccion', 'Datos de la Llamada')
                ->orderBy('orden')
                ->get()
                ->toArray();
        } else {
            session()->flash('error', 'No se encontró ninguna llamada con ese ID CIC');
            $this->paso = 0; // Volver al paso de selección
        }
    }

    protected function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function guardarDatos()
    {
        // Validar que haya datos
        if (!$this->idCic) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No hay datos de llamada cargados'
            ]);
            return;
        }

        // Validar Paso 3 antes de guardar
        foreach ($this->preguntasPaso3 as $pregunta) {
            $preguntaObj = (object) $pregunta;
            $valor = $this->respuestasPaso3[$preguntaObj->id] ?? null;
            if ($preguntaObj->requerido && ($valor === null || $valor === '')) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campos Incompletos',
                    'text' => "Por favor responde: {$preguntaObj->texto}"
                ]);
                return;
            }
        }

        // Validar Paso 4 antes de guardar
        foreach ($this->preguntasPaso4 as $pregunta) {
            $preguntaObj = (object) $pregunta;
            $valor = $this->respuestasPaso4[$preguntaObj->id] ?? null;
            if ($preguntaObj->requerido && ($valor === null || $valor === '')) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campos Incompletos',
                    'text' => "Por favor responde: {$preguntaObj->texto}"
                ]);
                return;
            }
        }

        // Validar Paso 5 antes de guardar
        foreach ($this->preguntasPaso5 as $pregunta) {
            $preguntaObj = (object) $pregunta;
            $valor = $this->respuestasPaso5[$preguntaObj->id] ?? null;
            if ($preguntaObj->requerido && ($valor === null || $valor === '')) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campos Incompletos',
                    'text' => "Por favor responde: {$preguntaObj->texto}"
                ]);
                return;
            }
        }

        // Validar Paso 6 antes de guardar
        foreach ($this->preguntasPaso6 as $pregunta) {
            $preguntaObj = (object) $pregunta;
            $valor = $this->respuestasPaso6[$preguntaObj->id] ?? null;
            if ($preguntaObj->requerido && ($valor === null || $valor === '')) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campos Incompletos',
                    'text' => "Por favor responde: {$preguntaObj->texto}"
                ]);
                return;
            }
        }

        // Validar Paso 7 antes de guardar
        foreach ($this->preguntasPaso7 as $pregunta) {
            $preguntaObj = (object) $pregunta;
            $valor = $this->respuestasPaso7[$preguntaObj->id] ?? null;
            if ($preguntaObj->requerido && ($valor === null || $valor === '')) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campos Incompletos',
                    'text' => "Por favor responde: {$preguntaObj->texto}"
                ]);
                return;
            }
        }

        // Validar Paso 8 antes de guardar
        foreach ($this->preguntasPaso8 as $pregunta) {
            $preguntaObj = (object) $pregunta;
            $valor = $this->respuestasPaso8[$preguntaObj->id] ?? null;
            if ($preguntaObj->requerido && ($valor === null || $valor === '')) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campos Incompletos',
                    'text' => "Por favor responde: {$preguntaObj->texto}"
                ]);
                return;
            }
        }

        // Validar Paso 9 antes de guardar
        foreach ($this->preguntasPaso9 as $pregunta) {
            $preguntaObj = (object) $pregunta;
            $valor = $this->respuestasPaso9[$preguntaObj->id] ?? null;
            if ($preguntaObj->requerido && ($valor === null || $valor === '')) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campos Incompletos',
                    'text' => "Por favor responde: {$preguntaObj->texto}"
                ]);
                return;
            }
        }

        // Validar Paso 10 antes de guardar
        foreach ($this->preguntasPaso10 as $pregunta) {
            $preguntaObj = (object) $pregunta;
            $valor = $this->respuestasPaso10[$preguntaObj->id] ?? null;
            // Solo validar si la pregunta es requerida
            if ($preguntaObj->requerido && ($valor === null || $valor === '')) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campos Incompletos',
                    'text' => "Por favor responde: {$preguntaObj->texto}"
                ]);
                return;
            }
        }

        // Validar Paso 11 antes de guardar - solo validar respuestas que existen
        if (!empty($this->respuestasPaso11)) {
            $idsConRespuesta = array_keys($this->respuestasPaso11);
            $preguntasP11 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('id', $idsConRespuesta)
                ->where('requerido', 1)
                ->get();

            foreach ($preguntasP11 as $pregunta) {
                $valor = $this->respuestasPaso11[$pregunta->id] ?? null;
                if ($valor === null || $valor === '') {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$pregunta->texto}"
                    ]);
                    return;
                }
            }
        }


        // Validar Paso 12 antes de guardar
        foreach ($this->preguntasPaso12 as $pregunta) {
            $preguntaObj = (object) $pregunta;
            $valor = $this->respuestasPaso12[$preguntaObj->id] ?? null;
            if ($preguntaObj->requerido && ($valor === null || $valor === '')) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campos Incompletos',
                    'text' => "Por favor responde: {$preguntaObj->texto}"
                ]);
                return;
            }
        }

        try {
            // Crear auditoría
            $auditoriaId = DB::table('raz_auditorias_calidad')->insertGetId([
                'formulario_id' => 1,
                'dni_auditor' => session('dni'),
                'estado' => 'completada', // Corregido: valor válido según migración
                'metadata' => json_encode([
                    'id_cic' => $this->idCic,
                    'telefono' => $this->telefono,
                    'fecha_llamada' => $this->fechaLlamada,
                    'fecha_monitoreo' => $this->fechaMonitoreo,
                    'duracion_segundos' => $this->duracionSegundos,
                    'duracion_formato' => $this->duracionFormato,
                    'nombre_asesor' => $this->nombreAsesor,
                    'usuario_asesor' => $this->usuarioAsesor,
                    'campana' => $this->campana,
                ]),
                'completada_at' => DB::raw('GETDATE()'),
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);

            // Obtener preguntas del Paso 1
            $preguntas = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->where('seccion', 'Datos de la Llamada')
                ->orderBy('orden')
                ->get();

            // Mapear respuestas
            $respuestas = [
                'Nombre del Analista' => $this->nombreAnalista,
                'ID de Interacción (CIC)' => $this->idCic,
                'Teléfono' => $this->telefono,
                'Fecha de Llamada' => $this->fechaLlamada,
                'Fecha de Monitoreo' => $this->fechaMonitoreo,
                'Duración de Llamada' => $this->duracionFormato,
                'Nombre del Asesor' => $this->nombreAsesor,
                'Usuario del Asesor' => $this->usuarioAsesor,
                'Campaña' => $this->campana,
            ];

            // Guardar respuestas del Paso 1
            foreach ($preguntas as $pregunta) {
                $valor = $respuestas[$pregunta->texto] ?? '';

                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $pregunta->id,
                    'valor' => $valor,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Guardar respuestas del Paso 2
            foreach ($this->respuestasPaso2 as $preguntaId => $respuesta) {
                if (!empty($respuesta)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntaId,
                        'valor' => $respuesta,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            // Guardar respuestas del Paso 3
            foreach ($this->respuestasPaso3 as $preguntaId => $valor) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntaId,
                    'valor' => is_array($valor) ? json_encode($valor) : $valor,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Guardar respuestas del Paso 4
            foreach ($this->respuestasPaso4 as $preguntaId => $valor) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntaId,
                    'valor' => is_array($valor) ? json_encode($valor) : $valor,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Guardar respuestas del Paso 5
            foreach ($this->respuestasPaso5 as $preguntaId => $valor) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntaId,
                    'valor' => is_array($valor) ? json_encode($valor) : $valor,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Guardar respuestas del Paso 6
            foreach ($this->respuestasPaso6 as $preguntaId => $valor) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntaId,
                    'valor' => is_array($valor) ? json_encode($valor) : $valor,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Guardar respuestas del Paso 7
            foreach ($this->respuestasPaso7 as $preguntaId => $valor) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntaId,
                    'valor' => is_array($valor) ? json_encode($valor) : $valor,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Guardar respuestas del Paso 8
            foreach ($this->respuestasPaso8 as $preguntaId => $valor) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntaId,
                    'valor' => is_array($valor) ? json_encode($valor) : $valor,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Guardar respuestas del Paso 9
            foreach ($this->respuestasPaso9 as $preguntaId => $valor) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntaId,
                    'valor' => is_array($valor) ? json_encode($valor) : $valor,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Guardar respuestas del Paso 10
            foreach ($this->respuestasPaso10 as $preguntaId => $valor) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntaId,
                    'valor' => is_array($valor) ? json_encode($valor) : $valor,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Guardar respuestas del Paso 11 - solo guardar IDs que existen en la base de datos
            $idsValidosPaso11 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('id', array_keys($this->respuestasPaso11))
                ->pluck('id')
                ->toArray();

            foreach ($this->respuestasPaso11 as $preguntaId => $valor) {
                // Solo guardar si el ID realmente existe en la base de datos
                if (in_array($preguntaId, $idsValidosPaso11)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntaId,
                        'valor' => is_array($valor) ? json_encode($valor) : $valor,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            // Guardar respuestas del Paso 12
            foreach ($this->respuestasPaso12 as $preguntaId => $respuesta) {
                if (!empty($respuesta)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntaId,
                        'valor' => $respuesta,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            $this->auditoriaId = $auditoriaId;
            $this->guardado = true; // Marcar como guardado
            $this->paso = 99; // Paso final: Pantalla de éxito

        } catch (\Exception $e) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al guardar los datos: ' . $e->getMessage()
            ]);
        }
    }

    public function siguiente()
    {
        // Validar Paso 2 antes de avanzar
        if ($this->paso === 2) {
            foreach ($this->preguntasPaso2 as $pregunta) {
                // Convertir a objeto si es array (por si acaso)
                $preguntaObj = (object) $pregunta;

                if ($preguntaObj->requerido && empty($this->respuestasPaso2[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }
        }

        // Validar Paso 3 antes de avanzar
        if ($this->paso === 3) {
            foreach ($this->preguntasPaso3 as $pregunta) {
                $preguntaObj = (object) $pregunta;
                if ($preguntaObj->requerido && empty($this->respuestasPaso3[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }
        }

        // Validar Paso 4 antes de avanzar
        if ($this->paso === 4) {
            foreach ($this->preguntasPaso4 as $pregunta) {
                $preguntaObj = (object) $pregunta;
                if ($preguntaObj->requerido && empty($this->respuestasPaso4[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }
        }

        // Validar Paso 5 antes de avanzar
        if ($this->paso === 5) {
            foreach ($this->preguntasPaso5 as $pregunta) {
                $preguntaObj = (object) $pregunta;
                if ($preguntaObj->requerido && empty($this->respuestasPaso5[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }
        }

        // Validar Paso 6 antes de avanzar
        if ($this->paso === 6) {
            foreach ($this->preguntasPaso6 as $pregunta) {
                $preguntaObj = (object) $pregunta;
                if ($preguntaObj->requerido && empty($this->respuestasPaso6[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }
        }

        // Validar Paso 7 antes de avanzar
        if ($this->paso === 7) {
            foreach ($this->preguntasPaso7 as $pregunta) {
                $preguntaObj = (object) $pregunta;
                if ($preguntaObj->requerido && empty($this->respuestasPaso7[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }
        }

        // Validar Paso 8 antes de avanzar
        if ($this->paso === 8) {
            foreach ($this->preguntasPaso8 as $pregunta) {
                $preguntaObj = (object) $pregunta;
                if ($preguntaObj->requerido && empty($this->respuestasPaso8[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }
        }

        // Validar Paso 9 antes de avanzar
        if ($this->paso === 9) {
            foreach ($this->preguntasPaso9 as $pregunta) {
                $preguntaObj = (object) $pregunta;
                if ($preguntaObj->requerido && empty($this->respuestasPaso9[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }
        }

        // Validar Paso 10 antes de avanzar
        if ($this->paso === 10) {
            foreach ($this->preguntasPaso10 as $pregunta) {
                $preguntaObj = (object) $pregunta;
                if ($preguntaObj->requerido && empty($this->respuestasPaso10[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }
        }

        // Validar Paso 11 antes de avanzar
        if ($this->paso === 11) {
            foreach ($this->preguntasPaso11 as $pregunta) {
                $preguntaObj = (object) $pregunta;
                if ($preguntaObj->requerido && empty($this->respuestasPaso11[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }

            // Verificar si debe avanzar a P12 o quedarse en P11
            // Solo avanza a P12 si P9 fue "NO" (flujo No Venta con Proceso/Agente/Cliente)
            $respuestaPaso9 = null;
            if (!empty($this->respuestasPaso9)) {
                $respuestaPaso9 = reset($this->respuestasPaso9);
            }

            // Si P9 fue "SI" o "NO APLICA", NO avanzar (quedarse en P11 para guardar)
            if ($respuestaPaso9 === 'SI' || $respuestaPaso9 === 'NO APLICA') {
                return; // No incrementar paso, quedarse en P11
            }
            // Si P9 fue "NO", continuar normalmente para avanzar a P12
        }

        // Validar Paso 12 antes de avanzar
        if ($this->paso === 12) {
            foreach ($this->preguntasPaso12 as $pregunta) {
                $preguntaObj = (object) $pregunta;
                if ($preguntaObj->requerido && empty($this->respuestasPaso12[$preguntaObj->id])) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campos Incompletos',
                        'text' => "Por favor responde: {$preguntaObj->texto}"
                    ]);
                    return;
                }
            }
        }

        $this->paso++;
        $this->bloquearRandomizar = true;

        if ($this->paso === 2) {
            // Validar que haya datos
            if (!$this->idCic) {
                session()->flash('error', 'No hay datos de llamada cargados');
                $this->paso--; // Revertir paso
                return;
            }

            // Cargar preguntas del Paso 2 (segunda sección única - Índice 1)
            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            $seccionPaso2 = $secciones[1] ?? null; // Segunda sección (Índice 1)

            if ($seccionPaso2) {
                $this->preguntasPaso2 = DB::table('raz_preguntas_auditorias')
                    ->where('formulario_id', 1)
                    ->where('seccion', $seccionPaso2)
                    ->orderBy('orden')
                    ->get()
                    ->toArray();
            }

            // Inicializar respuestas vacías para Paso 2 si no existen
            foreach ($this->preguntasPaso2 as $pregunta) {
                if (!isset($this->respuestasPaso2[$pregunta->id])) {
                    $this->respuestasPaso2[$pregunta->id] = '';
                }
            }
        } elseif ($this->paso === 3) {
            // Cargar preguntas del Paso 3 (tercera sección única - Índice 2)
            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            $seccionPaso3 = $secciones[2] ?? null; // Tercera sección (Índice 2)

            if ($seccionPaso3) {
                $this->preguntasPaso3 = DB::table('raz_preguntas_auditorias')
                    ->where('formulario_id', 1)
                    ->where('seccion', $seccionPaso3)
                    ->orderBy('orden')
                    ->get()
                    ->toArray();
            }

            // Inicializar respuestas vacías para Paso 3 si no existen
            foreach ($this->preguntasPaso3 as $pregunta) {
                if (!isset($this->respuestasPaso3[$pregunta->id])) {
                    $this->respuestasPaso3[$pregunta->id] = '';
                }
            }
        } elseif ($this->paso === 4) {
            // Cargar preguntas del Paso 4 (Cuerpo: todo entre P3 y P5)
            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            // Lógica Robusta Ajustada (considerando 4 secciones extra + 3 P12 en la BD):
            // Total Cola Real = 17 secciones útiles + 4 extra + 3 P12 = 24 secciones
            // P12 (3) + P11 (5) + Extra (4) + P10 (3) + P9 (1) + P8 (1) + P7 (1) + P6 (2) + P5 (4) = 24

            // P4 = Todo antes de las últimas 24 secciones (y después de las 3 primeras)
            $seccionesPaso4 = array_slice($secciones, 3, -24);

            $this->preguntasPaso4 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('seccion', $seccionesPaso4)
                ->orderBy('seccion')
                ->orderBy('orden')
                ->get()
                ->toArray();

            // Inicializar respuestas vacías para Paso 4 si no existen
            foreach ($this->preguntasPaso4 as $pregunta) {
                if (!isset($this->respuestasPaso4[$pregunta->id])) {
                    $this->respuestasPaso4[$pregunta->id] = '';
                }
            }
        } elseif ($this->paso === 5) {
            // Cargar preguntas del Paso 5 (Cola Parte 1: 4 secciones antes de las últimas 20)
            // P5: 4 secciones. Empieza en -24.
            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            // Obtener secciones del Paso 5 (las primeras 4 de las últimas 24)
            $seccionesPaso5 = array_slice($secciones, -24, 4);

            $this->preguntasPaso5 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('seccion', $seccionesPaso5)
                ->orderBy('seccion')
                ->orderBy('orden')
                ->get()
                ->toArray();

            // Inicializar respuestas vacías para Paso 5 si no existen
            foreach ($this->preguntasPaso5 as $pregunta) {
                if (!isset($this->respuestasPaso5[$pregunta->id])) {
                    $this->respuestasPaso5[$pregunta->id] = '';
                }
            }
        } elseif ($this->paso === 6) {
            // Cargar preguntas del Paso 6 (Cola Parte 2: 2 secciones antes de las últimas 18)
            // P6: 2 secciones. Empieza en -20.
            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            // Obtener las secciones del Paso 6 (las primeras 2 de las últimas 20)
            $seccionesPaso6 = array_slice($secciones, -20, 2);

            $this->preguntasPaso6 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('seccion', $seccionesPaso6)
                ->orderBy('seccion')
                ->orderBy('orden')
                ->get()
                ->toArray();

            // Inicializar respuestas vacías para Paso 6 si no existen
            foreach ($this->preguntasPaso6 as $pregunta) {
                if (!isset($this->respuestasPaso6[$pregunta->id])) {
                    $this->respuestasPaso6[$pregunta->id] = '';
                }
            }
        } elseif ($this->paso === 7) {
            // Cargar preguntas del Paso 7 (Cola Parte 3: 1 sección antes de las últimas 17)
            // P7: 1 sección. Empieza en -18.
            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            // Obtener la sección del Paso 7
            $seccionesPaso7 = array_slice($secciones, -18, 1);

            $this->preguntasPaso7 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('seccion', $seccionesPaso7)
                ->orderBy('seccion')
                ->orderBy('orden')
                ->get()
                ->toArray();

            // Inicializar respuestas vacías para Paso 7 si no existen
            foreach ($this->preguntasPaso7 as $pregunta) {
                if (!isset($this->respuestasPaso7[$pregunta->id])) {
                    $this->respuestasPaso7[$pregunta->id] = '';
                }
            }
        } elseif ($this->paso === 8) {
            // Cargar preguntas del Paso 8 (Cola Parte 4: 1 sección antes de las últimas 16)
            // P8: 1 sección. Empieza en -17.
            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            // Obtener la sección del Paso 8
            $seccionesPaso8 = array_slice($secciones, -17, 1);

            $this->preguntasPaso8 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('seccion', $seccionesPaso8)
                ->orderBy('seccion')
                ->orderBy('orden')
                ->get()
                ->toArray();

            // Inicializar respuestas vacías para Paso 8 si no existen
            foreach ($this->preguntasPaso8 as $pregunta) {
                if (!isset($this->respuestasPaso8[$pregunta->id])) {
                    $this->respuestasPaso8[$pregunta->id] = '';
                }
            }
        } elseif ($this->paso === 9) {
            // Cargar preguntas del Paso 9 (Cola Parte 5: 1 sección antes de las últimas 15)
            // P9: 1 sección. Empieza en -16.
            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            // Obtener la sección del Paso 9
            $seccionesPaso9 = array_slice($secciones, -16, 1);

            $this->preguntasPaso9 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('seccion', $seccionesPaso9)
                ->orderBy('seccion')
                ->orderBy('orden')
                ->get()
                ->toArray();

            // Inicializar respuestas vacías para Paso 9 si no existen
            foreach ($this->preguntasPaso9 as $pregunta) {
                if (!isset($this->respuestasPaso9[$pregunta->id])) {
                    $this->respuestasPaso9[$pregunta->id] = '';
                }
            }
        } elseif ($this->paso === 10) {
            // Cargar preguntas del Paso 10 (Cola Parte 6: 3 secciones antes de las últimas 14)
            // P10: 3 secciones. Empieza en -15.
            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            // Obtener las 3 secciones del Paso 10
            $seccionesPaso10 = array_slice($secciones, -15, 3);

            // Determinar qué sección mostrar basado en la respuesta del Paso 9
            $respuestaPaso9 = null;
            if (!empty($this->respuestasPaso9)) {
                $respuestaPaso9 = reset($this->respuestasPaso9); // Asumiendo que solo hay una pregunta en P9
            }

            $seccionMostrar = '';
            if ($respuestaPaso9 === 'SI') {
                $seccionMostrar = 'P10 - Validación Venta';
            } elseif ($respuestaPaso9 === 'NO') {
                $seccionMostrar = 'P10 - Análisis No Venta';
            } elseif ($respuestaPaso9 === 'NO APLICA') {
                $seccionMostrar = 'P10 - Validación No Aplica';
            }

            $this->preguntasPaso10 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('seccion', $seccionesPaso10)
                ->where('seccion', $seccionMostrar) // Filtrar solo la sección relevante
                ->orderBy('seccion')
                ->orderBy('orden')
                ->get()
                ->toArray();

            // Inicializar respuestas vacías para Paso 10 si no existen
            foreach ($this->preguntasPaso10 as $pregunta) {
                if (!isset($this->respuestasPaso10[$pregunta->id])) {
                    $this->respuestasPaso10[$pregunta->id] = '';
                }
            }
        } elseif ($this->paso === 11) {
            // Cargar preguntas del Paso 11 (Cola Parte 7: 5 secciones antes de las 3 P12)
            // P11: 5 secciones. Empieza en -8 (antes de las 3 P12). (Saltamos las 4 extra que están entre -12 y -9)
            // Lógica condicional anidada basada en P9 y P10

            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            // Obtener las 5 secciones antes de las 3 P12 (últimas 8, tomar 5)
            $seccionesPaso11 = array_slice($secciones, -8, 5);

            // Determinar qué sección mostrar basado en P9 y P10
            $respuestaPaso9 = null;
            if (!empty($this->respuestasPaso9)) {
                $respuestaPaso9 = reset($this->respuestasPaso9);
            }

            $seccionMostrar = '';

            if ($respuestaPaso9 === 'SI') {
                // Si P9 = SI → Mostrar "Seguimiento Venta"
                $seccionMostrar = 'P11 - Seguimiento Venta';
            } elseif ($respuestaPaso9 === 'NO') {
                // Si P9 = NO → Verificar P10 (Causa Raíz)
                $respuestaPaso10 = null;
                if (!empty($this->respuestasPaso10)) {
                    // Buscar si alguna de las respuestas es una de las causas raíz
                    foreach ($this->respuestasPaso10 as $respuesta) {
                        if (in_array($respuesta, ['Proceso', 'Agente', 'Cliente'])) {
                            $respuestaPaso10 = $respuesta;
                            break;
                        }
                    }
                }

                if ($respuestaPaso10 === 'Proceso') {
                    $seccionMostrar = 'P11 - Análisis Proceso';
                } elseif ($respuestaPaso10 === 'Agente') {
                    $seccionMostrar = 'P11 - Análisis Agente';
                } elseif ($respuestaPaso10 === 'Cliente') {
                    $seccionMostrar = 'P11 - Análisis Cliente';
                }
            } elseif ($respuestaPaso9 === 'NO APLICA') {
                // Si P9 = NO APLICA → Mostrar "Seguimiento No Aplica"
                $seccionMostrar = 'P11 - Seguimiento No Aplica';
            }

            $this->preguntasPaso11 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('seccion', $seccionesPaso11)
                ->where('seccion', $seccionMostrar)
                ->orderBy('seccion')
                ->orderBy('orden')
                ->get()
                ->toArray();

            // Inicializar respuestas vacías para Paso 11 si no existen
            foreach ($this->preguntasPaso11 as $pregunta) {
                if (!isset($this->respuestasPaso11[$pregunta->id])) {
                    $this->respuestasPaso11[$pregunta->id] = '';
                }
            }
        } elseif ($this->paso === 12) {
            // Cargar preguntas del Paso 12 - Solo para flujo "No Venta" (Proceso/Agente/Cliente)
            // P12: 3 secciones separadas. Últimas 3 secciones de la BD.
            // Determinar cuál mostrar según la respuesta de P10

            $secciones = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->select('seccion', DB::raw('MIN(id) as min_id'))
                ->groupBy('seccion')
                ->orderBy('min_id')
                ->pluck('seccion')
                ->toArray();

            // P12 son las últimas 3 secciones
            $seccionesPaso12 = array_slice($secciones, -3);

            // Determinar qué sección mostrar basado en P10 (Causa Raíz)
            $respuestaPaso10 = null;
            if (!empty($this->respuestasPaso10)) {
                // Buscar si alguna de las respuestas es una de las causas raíz
                foreach ($this->respuestasPaso10 as $respuesta) {
                    if (in_array($respuesta, ['Proceso', 'Agente', 'Cliente'])) {
                        $respuestaPaso10 = $respuesta;
                        break;
                    }
                }
            }

            $seccionMostrar = '';
            if ($respuestaPaso10 === 'Proceso') {
                $seccionMostrar = 'P12 - Seguimiento Proceso';
            } elseif ($respuestaPaso10 === 'Agente') {
                $seccionMostrar = 'P12 - Seguimiento Agente';
            } elseif ($respuestaPaso10 === 'Cliente') {
                $seccionMostrar = 'P12 - Seguimiento Cliente';
            }

            $this->preguntasPaso12 = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->whereIn('seccion', $seccionesPaso12)
                ->where('seccion', $seccionMostrar) // Filtrar solo la sección relevante
                ->orderBy('seccion')
                ->orderBy('orden')
                ->get()
                ->toArray();

            // Inicializar respuestas vacías para Paso 12 si no existen
            foreach ($this->preguntasPaso12 as $pregunta) {
                if (!isset($this->respuestasPaso12[$pregunta->id])) {
                    $this->respuestasPaso12[$pregunta->id] = '';
                }
            }
        }
    }

    public function retroceder()
    {
        if ($this->paso > 0) {
            $this->paso--;
        }
    }

    public function resetear()
    {
        // Resetear todo y volver al paso 0
        $this->paso = 0;
        $this->guardado = false;
        $this->nombreAnalista = null;
        $this->idCic = null;
        $this->telefono = null;
        $this->fechaLlamada = null;
        $this->fechaMonitoreo = null;
        $this->duracionSegundos = null;
        $this->duracionFormato = null;
        $this->nombreAsesor = null;
        $this->usuarioAsesor = null;
        $this->campana = null;
        $this->idCicManual = '';
        $this->filtroCampana = '';
        $this->filtroDni = '';
        $this->filtroFecha = '';

        // Confirmar que se ejecutó
        $this->dispatch('showAlert', [
            'type' => 'success',
            'title' => 'Reseteado',
            'text' => 'Volviendo a la selección de llamadas'
        ]);
    }

    public function limpiarFiltros()
    {
        $this->filtroCampana = '';
        $this->filtroDni = '';
        $this->filtroFecha = '';
    }

    // Propiedad computada para el progreso
    public function getProgressProperty()
    {
        // Total de pasos estimados (ajustar según el flujo más largo)
        $totalPasos = 12;

        if ($this->paso === 99) {
            return 100;
        }

        // Cálculo simple: (Paso actual / Total) * 100
        // Aseguramos que no pase de 95% antes de finalizar
        $percentage = min(round(($this->paso / $totalPasos) * 100), 95);

        return $percentage;
    }

    public function render()
    {
        return view('livewire.formulario-auditoria', [
            'progress' => $this->progress
        ])->layout('components.layouts.app');
    }

    // Computed Properties for Grouped Questions
    public function getPreguntasPaso2GroupedProperty()
    {
        return collect($this->preguntasPaso2)->groupBy('seccion');
    }

    public function getPreguntasPaso4GroupedProperty()
    {
        return collect($this->preguntasPaso4)->groupBy('seccion');
    }

    public function getPreguntasPaso5GroupedProperty()
    {
        return collect($this->preguntasPaso5)->groupBy('seccion');
    }

    public function getPreguntasPaso6GroupedProperty()
    {
        return collect($this->preguntasPaso6)->groupBy('seccion');
    }

    public function getPreguntasPaso7GroupedProperty()
    {
        return collect($this->preguntasPaso7)->groupBy('seccion');
    }

    public function getPreguntasPaso10GroupedProperty()
    {
        return collect($this->preguntasPaso10)->groupBy('seccion');
    }

    public function getPreguntasPaso11GroupedProperty()
    {
        return collect($this->preguntasPaso11)->groupBy('seccion');
    }

    public function getPreguntasPaso12GroupedProperty()
    {
        return collect($this->preguntasPaso12)->groupBy('seccion');
    }
    public function limpiarDatosIrrelevantes()
    {
        // 1. Verificar P9 (Validación Venta)
        $respuestaPaso9 = !empty($this->respuestasPaso9) ? reset($this->respuestasPaso9) : null;

        if ($respuestaPaso9 === 'SI') {
            // Camino: Venta -> P10 (Validación Venta) -> P11 (Seguimiento Venta) -> Fin
            // Limpiar P10: Solo dejar preguntas de "P10 - Validación Venta"
            if (!empty($this->respuestasPaso10)) {
                $preguntasP10Venta = DB::table('raz_preguntas_auditorias')
                    ->where('formulario_id', 1)
                    ->where('seccion', 'P10 - Validación Venta')
                    ->pluck('id')
                    ->toArray();

                foreach ($this->respuestasPaso10 as $preguntaId => $valor) {
                    if (!in_array($preguntaId, $preguntasP10Venta)) {
                        unset($this->respuestasPaso10[$preguntaId]);
                    }
                }
            }

            $this->respuestasPaso12 = []; // No hay P12

            // Limpiar P11: Solo dejar preguntas de "Seguimiento Venta"
            $this->filtrarRespuestasPaso11('P11 - Seguimiento Venta');

        } elseif ($respuestaPaso9 === 'NO APLICA') {
            // Camino: No Aplica -> P10 (Validación No Aplica) -> P11 (Seguimiento No Aplica) -> Fin
            // NO borrar respuestasPaso10 porque sí se usa en este camino
            $this->respuestasPaso12 = []; // No hay P12

            // Limpiar P10: Solo dejar preguntas de "P10 - Validación No Aplica"
            if (!empty($this->respuestasPaso10)) {
                $preguntasP10NoAplica = DB::table('raz_preguntas_auditorias')
                    ->where('formulario_id', 1)
                    ->where('seccion', 'P10 - Validación No Aplica')
                    ->pluck('id')
                    ->toArray();

                foreach ($this->respuestasPaso10 as $preguntaId => $valor) {
                    if (!in_array($preguntaId, $preguntasP10NoAplica)) {
                        unset($this->respuestasPaso10[$preguntaId]);
                    }
                }
            }

            // Limpiar P11: Solo dejar preguntas de "Seguimiento No Aplica"
            $this->filtrarRespuestasPaso11('P11 - Seguimiento No Aplica');

        } elseif ($respuestaPaso9 === 'NO') {
            // Camino: No Venta -> P10 -> P11 -> P12

            // Verificar P10 (Causa Raíz)
            $respuestaPaso10 = null;
            foreach ($this->respuestasPaso10 as $respuesta) {
                if (in_array($respuesta, ['Proceso', 'Agente', 'Cliente'])) {
                    $respuestaPaso10 = $respuesta;
                    break;
                }
            }

            if ($respuestaPaso10 === 'Proceso') {
                $this->filtrarRespuestasPaso11('P11 - Análisis Proceso');
                $this->filtrarRespuestasPaso12('P12 - Seguimiento Proceso');
            } elseif ($respuestaPaso10 === 'Agente') {
                $this->filtrarRespuestasPaso11('P11 - Análisis Agente');
                $this->filtrarRespuestasPaso12('P12 - Seguimiento Agente');
            } elseif ($respuestaPaso10 === 'Cliente') {
                $this->filtrarRespuestasPaso11('P11 - Análisis Cliente');
                $this->filtrarRespuestasPaso12('P12 - Seguimiento Cliente');
            }
        }
    }

    private function filtrarRespuestasPaso11($seccionCorrecta)
    {
        // Obtener IDs de preguntas de la sección correcta
        $idsValidos = DB::table('raz_preguntas_auditorias')
            ->where('formulario_id', 1)
            ->where('seccion', $seccionCorrecta)
            ->pluck('id')
            ->toArray();

        // Filtrar respuestas que no estén en los IDs válidos
        $this->respuestasPaso11 = array_intersect_key(
            $this->respuestasPaso11,
            array_flip($idsValidos)
        );
    }

    private function filtrarRespuestasPaso12($seccionCorrecta)
    {
        // Obtener IDs de preguntas de la sección correcta
        $idsValidos = DB::table('raz_preguntas_auditorias')
            ->where('formulario_id', 1)
            ->where('seccion', $seccionCorrecta)
            ->pluck('id')
            ->toArray();

        // Filtrar respuestas que no estén en los IDs válidos
        $this->respuestasPaso12 = array_intersect_key(
            $this->respuestasPaso12,
            array_flip($idsValidos)
        );
    }

    // Computed Properties para cargar preguntas dinámicamente
    public function getPreguntasPaso10Property()
    {
        $respuestaPaso9 = !empty($this->respuestasPaso9) ? reset($this->respuestasPaso9) : null;

        if ($respuestaPaso9 === 'SI') {
            // Rama SI: Validación Venta
            return DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->where('seccion', 'P10 - Validación Venta')
                ->orderBy('orden')
                ->get()
                ->toArray();
        } elseif ($respuestaPaso9 === 'NO APLICA') {
            // Rama NO APLICA: Validación No Aplica
            return DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->where('seccion', 'P10 - Validación No Aplica')
                ->orderBy('orden')
                ->get()
                ->toArray();
        } elseif ($respuestaPaso9 === 'NO') {
            // Rama NO: Análisis No Venta
            return DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->where('seccion', 'P10 - Análisis No Venta')
                ->orderBy('orden')
                ->get()
                ->toArray();
        }

        return [];
    }

    public function getPreguntasPaso11Property()
    {
        $respuestaPaso9 = !empty($this->respuestasPaso9) ? reset($this->respuestasPaso9) : null;

        if ($respuestaPaso9 === 'SI') {
            // Rama SI: Seguimiento Venta
            return DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->where('seccion', 'P11 - Seguimiento Venta')
                ->orderBy('orden')
                ->get()
                ->toArray();
        } elseif ($respuestaPaso9 === 'NO APLICA') {
            // Rama NO APLICA: Seguimiento No Aplica
            return DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->where('seccion', 'P11 - Seguimiento No Aplica')
                ->orderBy('orden')
                ->get()
                ->toArray();
        } elseif ($respuestaPaso9 === 'NO') {
            // Rama NO: Análisis según causa raíz
            $respuestaPaso10 = null;
            foreach ($this->respuestasPaso10 as $respuesta) {
                if (in_array($respuesta, ['Proceso', 'Agente', 'Cliente'])) {
                    $respuestaPaso10 = $respuesta;
                    break;
                }
            }

            if ($respuestaPaso10 === 'Proceso') {
                $seccion = 'P11 - Análisis Proceso';
            } elseif ($respuestaPaso10 === 'Agente') {
                $seccion = 'P11 - Análisis Agente';
            } elseif ($respuestaPaso10 === 'Cliente') {
                $seccion = 'P11 - Análisis Cliente';
            } else {
                return [];
            }

            return DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 1)
                ->where('seccion', $seccion)
                ->orderBy('orden')
                ->get()
                ->toArray();
        }

        return [];
    }

    public function getPreguntasPaso12Property()
    {
        $respuestaPaso9 = !empty($this->respuestasPaso9) ? reset($this->respuestasPaso9) : null;

        // Solo rama NO tiene Paso 12
        if ($respuestaPaso9 !== 'NO') {
            return [];
        }

        $respuestaPaso10 = null;
        foreach ($this->respuestasPaso10 as $respuesta) {
            if (in_array($respuesta, ['Proceso', 'Agente', 'Cliente'])) {
                $respuestaPaso10 = $respuesta;
                break;
            }
        }

        if ($respuestaPaso10 === 'Proceso') {
            $seccion = 'P12 - Seguimiento Proceso';
        } elseif ($respuestaPaso10 === 'Agente') {
            $seccion = 'P12 - Seguimiento Agente';
        } elseif ($respuestaPaso10 === 'Cliente') {
            $seccion = 'P12 - Seguimiento Cliente';
        } else {
            return [];
        }

        return DB::table('raz_preguntas_auditorias')
            ->where('formulario_id', 1)
            ->where('seccion', $seccion)
            ->orderBy('orden')
            ->get()
            ->toArray();
    }

    // Limpiar respuestas cuando cambia la rama en Paso 9
    public function updatedRespuestasPaso9()
    {
        // Limpiar respuestas de pasos siguientes para evitar mezcla de datos
        $this->respuestasPaso10 = [];
        $this->respuestasPaso11 = [];
        $this->respuestasPaso12 = [];
    }
}
