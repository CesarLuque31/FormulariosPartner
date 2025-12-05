<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class FormularioHogar extends Component
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

    // IDs de auditoría
    public $llamadaId = null;

    // Paso 1: Datos de llamada
    public $nombreAnalista;
    public $idCic;
    public $telefono;
    public $fechaLlamada;
    public $fechaMonitoreo;
    public $duracionSegundos;
    public $duracionFormato;
    public $nombreAsesor;
    public $usuarioAsesor;
    public $campana;

    // Paso 1: Tipo de Monitoreo
    public $tipoMonitoreo = '';

    // Paso 2: Campos específicos de Hogar
    public $tipoGestion = ''; // INBOUND / OUTBOUND
    public $origen = ''; // TIENDA / WHATSAPP / DIGITAL (opcional)
    public $tipoGestion2 = ''; // Crosseling / Venta Nueva / Up selling / Sin Cobertura
    public $productoOfertado = ''; // HFC / GPON / DTH / WTTX
    public $productoOfrecidoDetalle = ''; // Texto libre

    // Paso 3: PENC - ASESOR: PROTOCOLOS // BUENAS PRACTICAS
    public $preguntasPaso3 = [];
    public $respuestasPaso3 = [];

    // Paso 4: Evaluación adicional
    public $preguntasPaso4 = [];
    public $respuestasPaso4 = [];

    // Paso 5: Gestión comercial
    public $preguntasPaso5 = [];
    public $respuestasPaso5 = [];

    // Paso 6: PEC CUMPLIMIENTO
    public $preguntasPaso6 = [];
    public $respuestasPaso6 = [];

    // Paso 7: Novedades Críticas
    public $novedadesCriticas = '';

    // Paso 8: Concretó la venta
    public $concretoVenta = '';

    // Paso 9: ¿Se instaló el servicio? (rama SI/NO APLICA)
    public $seInstaloServicio = '';
    public $porqueNoInstalo = ''; // Solo si seInstaloServicio === 'NO'

    // Paso 9: Seguimiento (aplica para todas las respuestas)
    public $asesorRealizoSeguimiento = '';
    public $ventaFueRecuperada = '';
    public $solicitudFueIngresadaMesesAnteriores = '';

    // Paso 11: Observaciones PostVenta
    public $observacionesPostVenta = '';

    // Paso 9 (rama NO): Causa Raíz Principal
    public $causaRaizPrincipal = '';
    public $causaRaizDetalle = ''; // Campo de texto para Proceso/Agente/Cliente

    // Control de ramificación
    public $ramaFlujo = '';

    // Progreso
    public $progress = 0;
    public $auditoriaId = null;

    public function mount($idCic = null)
    {
        \Illuminate\Support\Facades\Log::info('FormularioHogar mount started', ['session_dni' => session('dni'), 'idCic' => $idCic]);

        // Detectar si viene un idCicManual desde la sesión (desde FormularioAuditoria)
        if (!$idCic && session()->has('idCicManual')) {
            $idCic = session()->pull('idCicManual'); // pull para sacarlo de la sesión
        }

        // Obtener nombre del analista desde sesión
        $empleado = DB::table('pri.empleados')
            ->where('DNI', session('dni'))
            ->first();

        if ($empleado) {
            $this->nombreAnalista = trim(
                ($empleado->Nombres ?? '') . ' ' .
                ($empleado->ApellidoPaterno ?? '') . ' ' .
                ($empleado->ApellidoMaterno ?? '')
            );
        }

        // Fecha de monitoreo = hoy
        $this->fechaMonitoreo = date('Y-m-d');

        // Cargar campañas disponibles para Hogar
        $this->campanasDisponibles = DB::table('dbo.Reporte_Llamadas_Detalle')
            ->where('Fecha', '>=', '2025-10-01')
            ->whereNotNull('Campaña_Agente')
            ->where('Campaña_Agente', '!=', '')
            ->where(function ($query) {
                $query->where('Campaña_Agente', 'LIKE', '%Hogar%')
                    ->orWhere('Campaña_Agente', 'LIKE', '%hogar%')
                    ->orWhere('Campaña_Agente', 'LIKE', '%HOGAR%');
            })
            ->distinct()
            ->pluck('Campaña_Agente')
            ->sort()
            ->values()
            ->toArray();

        // Si se pasa un idCic, cargarlo directamente
        if ($idCic) {
            $this->idCicManual = $idCic;
            $this->cargarLlamadaPorCic($idCic);
            $this->paso = 1;
            $this->bloquearRandomizar = true;
        }

        // Cargar preguntas del Paso 3
        $this->cargarPreguntasPaso3();

        // Cargar preguntas del Paso 4
        $this->cargarPreguntasPaso4();

        // Cargar preguntas del Paso 5
        $this->cargarPreguntasPaso5();

        // Cargar preguntas del Paso 6
        $this->cargarPreguntasPaso6();
    }

    private function cargarPreguntasPaso3()
    {
        // Definir preguntas del Paso 3: PENC - ASESOR: PROTOCOLOS // BUENAS PRACTICAS
        $this->preguntasPaso3 = [
            [
                'seccion' => 'Aplica Script Establecido',
                'preguntas' => [
                    'Aplica Script Establecido',
                    'Saludo Despido',
                    'Indica su nombre'
                ]
            ],
            [
                'seccion' => 'Escucha activa',
                'preguntas' => [
                    'Desconcentración',
                    'Interrupciones'
                ]
            ],
            [
                'seccion' => 'Fórmulas de Cortesía',
                'preguntas' => [
                    'Personaliza la llamada',
                    'Tono de voz, dicción, volumen de voz, vocabulario',
                    'Amabilidad / Empatía'
                ]
            ]
        ];
    }

    private function cargarPreguntasPaso4()
    {
        // Definir preguntas del Paso 4
        $this->preguntasPaso4 = [
            [
                'seccion' => 'Información / solución correcta y completa',
                'preguntas' => [
                    'Información correcta/completa del producto ofrecido'
                ]
            ],
            [
                'seccion' => 'Procesos y Registros',
                'preguntas' => [
                    'Correcto proceso de coordinación',
                    'Proceso correctamente en los aplicativos'
                ]
            ],
            [
                'seccion' => 'Actitud del servicio',
                'preguntas' => [
                    'Llamada incompleta/cierre de llamada',
                    'Evita llenar a otro canal',
                    'Canal abierto'
                ]
            ],
            [
                'seccion' => 'Calidad de atención',
                'preguntas' => [
                    'Saluda y agradece la Espera',
                    'Tiempo de Espera y uso del hold'
                ]
            ]
        ];
    }

    private function cargarPreguntasPaso5()
    {
        // Definir preguntas del Paso 5: Gestión comercial
        $this->preguntasPaso5 = [
            [
                'seccion' => 'Gestión comercial',
                'preguntas' => [
                    'Seguimiento de Gestión',
                    'Validación de datos',
                    'Sonidos correctamente registrados',
                    'Ofrecimiento acorde a la necesidad/necesitada',
                    'Valida correctamente cobertura',
                    'Rebate objeciones',
                    'Despeja dudas del cliente',
                    'Incentiva a la baja',
                    'Ofrecimiento de promoción vigente',
                    'Ofrecimiento convergente',
                    'Registro correcto y completo en aplicativos'
                ]
            ]
        ];
    }

    private function cargarPreguntasPaso6()
    {
        // Definir preguntas del Paso 6: PEC CUMPLIMIENTO
        $this->preguntasPaso6 = [
            [
                'seccion' => 'Valida identidad para entregar información',
                'preguntas' => [
                    'Valida identidad para entregar información',
                    'Resumen Completo de la Venta',
                    'Confirma Aceptación del Cliente',
                    'Indica que llamada está siendo grabada',
                    'Solicita permiso para dar información comercial'
                ]
            ]
        ];
    }

    public function cargarLlamadaAleatoria()
    {
        $query = DB::table('dbo.Reporte_Llamadas_Detalle')
            ->where('Fecha', '>=', '2025-10-01')
            ->whereNotNull('Campaña_Agente')
            ->where('Campaña_Agente', '!=', '')
            ->where(function ($q) {
                $q->where('Campaña_Agente', 'LIKE', '%Hogar%')
                    ->orWhere('Campaña_Agente', 'LIKE', '%hogar%')
                    ->orWhere('Campaña_Agente', 'LIKE', '%HOGAR%');
            });

        // Aplicar filtros opcionales
        if (!empty($this->filtroCampana)) {
            $query->where('Campaña_Agente', $this->filtroCampana);
        }

        if (!empty($this->filtroDni)) {
            $query->where('DNI_Empleado', $this->filtroDni);
        }

        if (!empty($this->filtroFecha)) {
            $query->whereDate('Fecha', $this->filtroFecha);
        }

        $llamada = $query->inRandomOrder()->first();

        if ($llamada) {
            $this->mapearDatosLlamada($llamada);
        } else {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => 'Sin resultados',
                'text' => 'No se encontraron llamadas de Hogar con los filtros aplicados'
            ]);
        }
    }

    private function cargarLlamadaPorCic($idCic)
    {
        $llamada = DB::table('dbo.Reporte_Llamadas_Detalle')
            ->where('ID_Largo', $idCic)
            ->first();

        if ($llamada) {
            $this->mapearDatosLlamada($llamada);
        } else {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se encontró la llamada con ID CIC: ' . $idCic
            ]);
        }
    }

    private function mapearDatosLlamada($llamada)
    {
        $this->idCic = $llamada->ID_Largo ?? '';
        $this->telefono = $llamada->Numero ?? '';
        $this->fechaLlamada = $llamada->Fecha ? date('Y-m-d', strtotime($llamada->Fecha)) : '';
        $this->duracionSegundos = $llamada->Duracion ?? 0;
        $this->duracionFormato = gmdate('H:i:s', $this->duracionSegundos);
        $this->nombreAsesor = $llamada->NombreCompletoAgente ?? '';
        $this->usuarioAsesor = $llamada->Usuario_Llamada_Origen ?? '';
        $this->campana = $llamada->Campaña_Agente ?? '';
    }

    public function seleccionarAleatorio()
    {
        if (!empty($this->filtroFecha)) {
            $this->validate([
                'filtroFecha' => 'date|after_or_equal:2025-10-01|before_or_equal:today',
            ], [
                'filtroFecha.after_or_equal' => 'La fecha debe ser desde Octubre 2025.',
                'filtroFecha.before_or_equal' => 'La fecha no puede ser futura.',
            ]);

            if ($this->getErrorBag()->has('filtroFecha')) {
                return;
            }
        }

        $this->cargarLlamadaAleatoria();
        $this->paso = 1;
    }

    public function limpiarFiltros()
    {
        $this->filtroCampana = '';
        $this->filtroDni = '';
        $this->filtroFecha = '';
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

    public function siguiente()
    {
        // Validar según el paso actual
        if ($this->paso === 1) {
            $this->validate([
                'tipoMonitoreo' => 'required'
            ], [
                'tipoMonitoreo.required' => 'Debe seleccionar el tipo de monitoreo'
            ]);
        }

        if ($this->paso === 2) {
            $this->validate([
                'tipoGestion' => 'required',
                // origen es opcional
                'tipoGestion2' => 'required',
                'productoOfertado' => 'required',
                'productoOfrecidoDetalle' => 'required'
            ], [
                'tipoGestion.required' => 'Debe seleccionar el tipo de gestión',
                'tipoGestion2.required' => 'Debe seleccionar el tipo de gestión',
                'productoOfertado.required' => 'Debe seleccionar el producto ofertado',
                'productoOfrecidoDetalle.required' => 'Debe detallar el producto ofrecido'
            ]);
        }

        if ($this->paso === 3) {
            // Validar que todas las preguntas del paso 3 estén respondidas
            foreach ($this->preguntasPaso3 as $section) {
                foreach ($section['preguntas'] as $pregunta) {
                    $resp = $this->respuestasPaso3[$pregunta] ?? null;
                    if (empty($resp)) {
                        $this->dispatch('showAlert', [
                            'type' => 'warning',
                            'title' => 'Campos Incompletos',
                            'text' => "Debe responder: {$pregunta}"
                        ]);
                        return;
                    }
                }
            }
        }

        if ($this->paso === 4) {
            // Validar que todas las preguntas del paso 4 estén respondidas
            foreach ($this->preguntasPaso4 as $section) {
                foreach ($section['preguntas'] as $pregunta) {
                    $resp = $this->respuestasPaso4[$pregunta] ?? null;
                    if (empty($resp)) {
                        $this->dispatch('showAlert', [
                            'type' => 'warning',
                            'title' => 'Campos Incompletos',
                            'text' => "Debe responder: {$pregunta}"
                        ]);
                        return;
                    }
                }
            }
        }

        if ($this->paso === 5) {
            // Validar que todas las preguntas del paso 5 estén respondidas
            foreach ($this->preguntasPaso5 as $section) {
                foreach ($section['preguntas'] as $pregunta) {
                    $resp = $this->respuestasPaso5[$pregunta] ?? null;
                    if (empty($resp)) {
                        $this->dispatch('showAlert', [
                            'type' => 'warning',
                            'title' => 'Campos Incompletos',
                            'text' => "Debe responder: {$pregunta}"
                        ]);
                        return;
                    }
                }
            }
        }

        if ($this->paso === 6) {
            // Validar que todas las preguntas del paso 6 estén respondidas
            foreach ($this->preguntasPaso6 as $section) {
                foreach ($section['preguntas'] as $pregunta) {
                    $resp = $this->respuestasPaso6[$pregunta] ?? null;
                    if (empty($resp)) {
                        $this->dispatch('showAlert', [
                            'type' => 'warning',
                            'title' => 'Campos Incompletos',
                            'text' => "Debe responder: {$pregunta}"
                        ]);
                        return;
                    }
                }
            }
        }

        if ($this->paso === 7) {
            // Validar que el campo de novedades críticas esté completado
            if (empty(trim($this->novedadesCriticas))) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe detallar las novedades críticas presentadas en la llamada'
                ]);
                return;
            }
        }

        if ($this->paso === 8) {
            // Validar que se haya seleccionado si concretó la venta
            if (empty($this->concretoVenta)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe indicar si concretó la venta en la llamada'
                ]);
                return;
            }

            // Determinar la rama según la respuesta
            if ($this->concretoVenta === 'SI' || $this->concretoVenta === 'NO APLICA') {
                $this->ramaFlujo = 'venta_si_no_aplica';
            } else if ($this->concretoVenta === 'NO') {
                $this->ramaFlujo = 'venta_no';
            }
        }

        // Paso 9 (rama NO): Validar causa raíz principal
        if ($this->paso === 9 && $this->ramaFlujo === 'venta_no') {
            if (empty($this->causaRaizPrincipal)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe seleccionar la causa raíz principal'
                ]);
                return;
            }
        }

        // Paso 10 (rama NO): Validar instalación del servicio
        if ($this->paso === 10 && $this->ramaFlujo === 'venta_no') {
            // El campo de detalle de causa raíz es OBLIGATORIO
            if (empty(trim($this->causaRaizDetalle))) {
                $causaTipo = $this->causaRaizPrincipal; // Proceso, Agente o Cliente
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => "Debe completar el campo de {$causaTipo}"
                ]);
                return;
            }

            if (empty($this->seInstaloServicio)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe indicar si se instaló el servicio'
                ]);
                return;
            }
        }

        if ($this->paso === 9 && $this->ramaFlujo === 'venta_si_no_aplica') {
            // Validar que se haya seleccionado si se instaló el servicio
            if (empty($this->seInstaloServicio)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe indicar si se instaló el servicio'
                ]);
                return;
            }
        }

        if ($this->paso === 10 && $this->ramaFlujo === 'venta_si_no_aplica') {
            // Si seleccionó NO en paso 9, validar campo de razón
            if ($this->seInstaloServicio === 'NO' && empty(trim($this->porqueNoInstalo))) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => '¿Por qué no instaló el servicio?'
                ]);
                return;
            }

            // Validar las 3 preguntas de seguimiento
            if (empty($this->asesorRealizoSeguimiento)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Asesor realizó Seguimiento?'
                ]);
                return;
            }

            if (empty($this->ventaFueRecuperada)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Venta fue recuperada?'
                ]);
                return;
            }

            if (empty($this->solicitudFueIngresadaMesesAnteriores)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Solicitud fue ingresada en meses anteriores?'
                ]);
                return;
            }
        }

        // Paso 11: Observaciones PostVenta (rama SI/NO APLICA)
        if ($this->paso === 11 && $this->ramaFlujo === 'venta_si_no_aplica') {
            // No hay validación, el campo es opcional
        }

        // Paso 11: Seguimiento (rama NO)
        if ($this->paso === 11 && $this->ramaFlujo === 'venta_no') {
            // Si seleccionó NO en paso 10, validar campo de razón
            if ($this->seInstaloServicio === 'NO' && empty(trim($this->porqueNoInstalo))) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => '¿Por qué no instaló el servicio?'
                ]);
                return;
            }

            // Validar las 3 preguntas de seguimiento
            if (empty($this->asesorRealizoSeguimiento)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Asesor realizó Seguimiento?'
                ]);
                return;
            }

            if (empty($this->ventaFueRecuperada)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Venta fue recuperada?'
                ]);
                return;
            }

            if (empty($this->solicitudFueIngresadaMesesAnteriores)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Solicitud fue ingresada en meses anteriores?'
                ]);
                return;
            }
        }

        // Paso 12: Observaciones PostVenta (rama NO)
        if ($this->paso === 12 && $this->ramaFlujo === 'venta_no') {
            // No hay validación, el campo es opcional
        }

        // Si pasa las validaciones, avanzar al siguiente paso
        $this->paso++;
        $this->calcularProgreso();
    }

    public function retroceder()
    {
        if ($this->paso > 0) {
            $this->paso--;
            $this->calcularProgreso();
        }
    }

    private function calcularProgreso()
    {
        // Total de pasos según la rama
        if ($this->ramaFlujo === 'venta_no') {
            $totalPasos = 13; // Pasos 0-8 + 9 + 10 + 11 + 12
        } elseif ($this->ramaFlujo === 'venta_si_no_aplica') {
            $totalPasos = 12; // Pasos 0-8 + 9 + 10 + 11
        } else {
            $totalPasos = 9; // Pasos 0-8 (antes de seleccionar rama)
        }

        $this->progress = round(($this->paso / $totalPasos) * 100);
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

        try {
            DB::beginTransaction();

            // Crear auditoría (igual que Digital, sin llamada_id)
            $auditoriaId = DB::table('raz_auditorias_calidad')->insertGetId([
                'formulario_id' => 5,
                'dni_auditor' => session('dni'),
                'estado' => 'completada',
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
                    'rama_flujo' => $this->ramaFlujo,
                ]),
                'completada_at' => DB::raw('GETDATE()'),
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);

            // Obtener todas las preguntas del formulario Hogar
            $preguntas = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 5)
                ->orderBy('orden')
                ->get()
                ->keyBy('texto');

            // PASO 0: Datos de la Llamada
            $respuestasPaso0 = [
                'Nombre del Analista' => $this->nombreAnalista,
                'ID de Interacción' => $this->idCic,
                'Teléfono' => $this->telefono,
                'Fecha de Llamada' => $this->fechaLlamada,
                'Fecha de Monitoreo' => $this->fechaMonitoreo,
                'Duración de Llamada' => $this->duracionFormato,
                'Nombre del Asesor' => $this->nombreAsesor,
                'Usuario del Asesor' => $this->usuarioAsesor,
                'Campaña' => $this->campana,
            ];

            foreach ($respuestasPaso0 as $texto => $valor) {
                if (isset($preguntas[$texto]) && !empty($valor)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas[$texto]->id,
                        'valor' => $valor,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            // PASO 1: Tipo de Monitoreo
            if (isset($preguntas['Tipo de Monitoreo']) && !empty($this->tipoMonitoreo)) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntas['Tipo de Monitoreo']->id,
                    'valor' => $this->tipoMonitoreo,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // PASO 2: Datos Hogar
            $respuestasPaso2 = [
                'Tipo de Gestión' => $this->tipoGestion,
                'Origen' => $this->origen,
                'Tipo de Gestión 2' => $this->tipoGestion2,
                'Producto Ofertado' => $this->productoOfertado,
                'Producto Ofrecido Detalle' => $this->productoOfrecidoDetalle,
            ];

            foreach ($respuestasPaso2 as $texto => $valor) {
                if (isset($preguntas[$texto]) && !empty($valor)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas[$texto]->id,
                        'valor' => $valor,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            // PASO 3, 4, 5, 6: Respuestas de evaluación
            foreach ($this->respuestasPaso3 as $texto => $valor) {
                if (isset($preguntas[$texto]) && !empty($valor)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas[$texto]->id,
                        'valor' => $this->convertirValorRadio($valor),
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            foreach ($this->respuestasPaso4 as $texto => $valor) {
                if (isset($preguntas[$texto]) && !empty($valor)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas[$texto]->id,
                        'valor' => $this->convertirValorRadio($valor),
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            foreach ($this->respuestasPaso5 as $texto => $valor) {
                if (isset($preguntas[$texto]) && !empty($valor)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas[$texto]->id,
                        'valor' => $this->convertirValorRadio($valor),
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            foreach ($this->respuestasPaso6 as $texto => $valor) {
                if (isset($preguntas[$texto]) && !empty($valor)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas[$texto]->id,
                        'valor' => $this->convertirValorRadio($valor),
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            // PASO 7: Novedades Críticas
            if (isset($preguntas['Novedades Críticas']) && !empty($this->novedadesCriticas)) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntas['Novedades Críticas']->id,
                    'valor' => $this->novedadesCriticas,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // PASO 8: Concretó la venta
            if (isset($preguntas['Concretó la venta en la llamada']) && !empty($this->concretoVenta)) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntas['Concretó la venta en la llamada']->id,
                    'valor' => $this->convertirValorRadio($this->concretoVenta),
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // GUARDADO POR RAMAS
            if ($this->ramaFlujo === 'venta_si_no_aplica') {
                $this->guardarRamaSiNoAplica($auditoriaId);
            } elseif ($this->ramaFlujo === 'venta_no') {
                $this->guardarRamaNo($auditoriaId);
            }

            DB::commit();

            $this->guardado = true;
            $this->paso = 99;

            $this->dispatch('showAlert', [
                'type' => 'success',
                'title' => '¡Auditoría Guardada!',
                'text' => 'La auditoría se ha registrado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => 'Error al guardar',
                'text' => 'Ocurrió un error: ' . $e->getMessage()
            ]);
        }
    }

    private function guardarRamaSiNoAplica($auditoriaId)
    {
        // Paso 9: ¿Se instaló el servicio?
        if (!empty($this->seInstaloServicio)) {
            $pregunta = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 5)
                ->where('seccion', 'Instalación (Rama SI/NO APLICA)')
                ->where('texto', '¿SE INSTALÓ EL SERVICIO?')
                ->first();

            if ($pregunta) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $pregunta->id,
                    'valor' => $this->convertirValorRadio($this->seInstaloServicio),
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }
        }

        // Paso 10: Seguimiento
        if ($this->seInstaloServicio === 'NO' && !empty($this->porqueNoInstalo)) {
            $pregunta = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 5)
                ->where('seccion', 'Seguimiento (Rama SI/NO APLICA)')
                ->where('texto', '¿Por qué no instaló el servicio?')
                ->first();

            if ($pregunta) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $pregunta->id,
                    'valor' => $this->porqueNoInstalo,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }
        }

        $respuestasSeguimiento = [
            'Asesor realizó Seguimiento?' => $this->asesorRealizoSeguimiento,
            'Venta fue recuperada?' => $this->ventaFueRecuperada,
            'Solicitud fue ingresada en meses anteriores' => $this->solicitudFueIngresadaMesesAnteriores,
        ];

        foreach ($respuestasSeguimiento as $texto => $valor) {
            if (!empty($valor)) {
                $pregunta = DB::table('raz_preguntas_auditorias')
                    ->where('formulario_id', 5)
                    ->where('seccion', 'Seguimiento (Rama SI/NO APLICA)')
                    ->where('texto', $texto)
                    ->first();

                if ($pregunta) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $pregunta->id,
                        'valor' => $this->convertirValorRadio($valor),
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }
        }

        // Paso 11: Observaciones PostVenta
        if (!empty($this->observacionesPostVenta)) {
            $pregunta = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 5)
                ->where('seccion', 'Observaciones PostVenta (Rama SI/NO APLICA)')
                ->where('texto', 'Detalla las observaciones en la PostVenta')
                ->first();

            if ($pregunta) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $pregunta->id,
                    'valor' => $this->observacionesPostVenta,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }
        }
    }

    private function guardarRamaNo($auditoriaId)
    {
        // Paso 9: Causa Raíz Principal
        $preguntas = DB::table('raz_preguntas_auditorias')
            ->where('formulario_id', 5)
            ->whereIn('seccion', ['Causa Raíz (Rama NO)', 'Detalle Causa Raíz (Rama NO)', 'Instalación (Rama NO)', 'Seguimiento (Rama NO)', 'Observaciones PostVenta (Rama NO)'])
            ->get()
            ->keyBy('texto');

        if (isset($preguntas['Causa Raíz Principal']) && !empty($this->causaRaizPrincipal)) {
            DB::table('raz_respuestas_auditorias')->insert([
                'auditoria_id' => $auditoriaId,
                'pregunta_id' => $preguntas['Causa Raíz Principal']->id,
                'valor' => $this->causaRaizPrincipal,
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);
        }

        // Paso 10: Detalle Causa Raíz
        if (isset($preguntas['Detalle Proceso/Agente/Cliente']) && !empty($this->causaRaizDetalle)) {
            DB::table('raz_respuestas_auditorias')->insert([
                'auditoria_id' => $auditoriaId,
                'pregunta_id' => $preguntas['Detalle Proceso/Agente/Cliente']->id,
                'valor' => $this->causaRaizDetalle,
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);
        }

        // Paso 10: ¿Se instaló el servicio?
        if (isset($preguntas['¿SE INSTALÓ EL SERVICIO?']) && !empty($this->seInstaloServicio)) {
            DB::table('raz_respuestas_auditorias')->insert([
                'auditoria_id' => $auditoriaId,
                'pregunta_id' => $preguntas['¿SE INSTALÓ EL SERVICIO?']->id,
                'valor' => $this->convertirValorRadio($this->seInstaloServicio),
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);
        }

        // Paso 11: Seguimiento
        if ($this->seInstaloServicio === 'NO' && !empty($this->porqueNoInstalo)) {
            $preguntaPorque = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 5)
                ->where('seccion', 'Seguimiento (Rama NO)')
                ->where('texto', '¿Por qué no instaló el servicio?')
                ->first();

            if ($preguntaPorque) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntaPorque->id,
                    'valor' => $this->porqueNoInstalo,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }
        }

        $respuestasSeguimiento = [
            'Asesor realizó Seguimiento?' => $this->asesorRealizoSeguimiento,
            'Venta fue recuperada?' => $this->ventaFueRecuperada,
            'Solicitud fue ingresada en meses anteriores' => $this->solicitudFueIngresadaMesesAnteriores,
        ];

        foreach ($respuestasSeguimiento as $texto => $valor) {
            if (!empty($valor) && isset($preguntas[$texto])) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntas[$texto]->id,
                    'valor' => $this->convertirValorRadio($valor),
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }
        }

        // Paso 12: Observaciones PostVenta
        if (!empty($this->observacionesPostVenta) && isset($preguntas['Detalla las observaciones en la PostVenta'])) {
            DB::table('raz_respuestas_auditorias')->insert([
                'auditoria_id' => $auditoriaId,
                'pregunta_id' => $preguntas['Detalla las observaciones en la PostVenta']->id,
                'valor' => $this->observacionesPostVenta,
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);
        }
    }

    private function convertirValorRadio($valor)
    {
        $mapa = [
            'SI' => '1',
            'NO' => '2',
            'NO APLICA' => '3',
        ];

        return $mapa[$valor] ?? $valor;
    }

    public function resetear()
    {
        return $this->redirect('/auditoria/nueva', navigate: true);
    }

    public function render()
    {
        return view('livewire.formulario-hogar')
            ->layout('components.layouts.app');
    }
}
