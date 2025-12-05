<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class FormularioDigital extends Component
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

    // Campos adicionales para Prepago Digital
    public $tipoGestion = ''; // INBOUND o OUTBOUND
    public $campanasDigital = ''; // Tienda 1-10, WHATSAPP DIGITAL, LLAMADAS DIGITAL
    public $tipoMonitoreo = ''; // Aleatorio, Auditoría, CODIGO DE CONCLUSION

    // Paso 3: Productos
    public $detalleAuditoria = ''; // Solo si tipoMonitoreo = Auditoría
    public $productoOfertadoFijo = ''; // Dropdown
    public $productoOfertadoMovil = ''; // Radio buttons

    // Paso 3: Comentarios y acciones (review / seguimiento)
    public $comentarios = '';
    public $acciones = '';
    // Paso 3: preguntas y respuestas (estructura estática basada en diseño)
    public $preguntasPaso3 = [];
    public $respuestasPaso3 = [];
    // Paso 4: evaluacion final (PEC-UF)
    public $preguntasPaso4 = [];
    public $respuestasPaso4 = [];
    // Paso 5: PEC-NEG
    public $preguntasPaso5 = [];
    public $respuestasPaso5 = [];
    // Paso 6: PEC CUM
    public $preguntasPaso6 = [];
    public $respuestasPaso6 = [];
    // Paso 7: Novedades Críticas
    public $novedadesCriticas = '';
    public $derivacionWhatsapp = ''; // CLIENTE SOLICITA / ASESOR DERIVA / NO APLICA
    // Paso 8: Concretó la venta
    public $concretoVenta = '';
    // Paso 9: Instalación (solo si concretó venta = SI)
    public $seInstaloServicio = '';
    public $seEntregoEquipoChip = '';
    public $seActivoChip = '';
    public $seEntregoEquipoMovil = '';
    public $seEntregoEquipoHogar = '';
    public $responsableEntrega = '';
    public $razonNoEntrega = '';
    public $asesorRealizoSeguimiento = '';
    public $ventaRecuperada = '';
    public $solicitudMesesAnteriores = '';
    public $solicitudCerradaOtroCanal = '';
    public $ventaNoContacto = '';
    // Paso 10: Observaciones PostVenta (solo si concretó venta = SI)
    public $observacionesPostVenta = '';
    // Paso 9B: Causa Raíz Principal (solo si concretó venta = NO)
    public $causaRaizPrincipal = '';
    // Paso 10: Detalles según Causa Raíz (Proceso/Agente/Cliente)
    public $detallesCausaRaiz = ''; // Campo de texto dinámico
    public $p10_seInstaloServicio = '';
    public $p10_seEntregoEquipoChip = '';
    public $p10_seActivoChip = '';
    public $p10_responsableEntrega = '';
    public $p10_razonNoEntrega = '';
    public $p10_asesorRealizoSeguimiento = '';
    public $p10_ventaRecuperada = '';
    public $p10_solicitudMesesAnteriores = '';
    // Paso 11: Observaciones PostVenta Final (rama NO)
    public $observacionesPostVentaFinal = '';
    // Variable para rastrear la rama del flujo
    public $ramaFlujo = ''; // 'venta_si' o 'venta_no'
    // Paso 2: Producto Ofertado
    public $productoOfertado = '';

    // Datos internos
    public $duracionSegundos;
    public $auditoriaId = null;
    public $formularioId = 4; // ID específico para formulario Prepago Digital

    public function mount($idCic = null)
    {
        \Illuminate\Support\Facades\Log::info('FormularioDigital mount started', ['session_dni' => session('dni'), 'idCic' => $idCic]);

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
        $this->fechaMonitoreo = now()->format('Y-m-d');

        // Cargar solo campañas de Prepago Digital
        $this->campanasDisponibles = DB::table('dbo.Reporte_Llamadas_Detalle')
            ->where('Fecha', '>=', '2025-10-01')
            ->whereNotNull('Campaña_Agente')
            ->where('Campaña_Agente', '!=', '')
            ->where(function ($query) {
                $query->where('Campaña_Agente', 'LIKE', '%Prepago Digital%')
                    ->orWhere('Campaña_Agente', 'LIKE', '%prepago digital%')
                    ->orWhere('Campaña_Agente', 'LIKE', '%PREPAGO DIGITAL%')
                    ->orWhere('Campaña_Agente', 'LIKE', '%Digital%');
            })
            ->distinct()
            ->pluck('Campaña_Agente')
            ->sort()
            ->values()
            ->toArray();

        // Inicializar preguntas estáticas del Paso 3 (según diseño)
        $this->preguntasPaso3 = [
            [
                'seccion' => 'Saluda / Se despide',
                'preguntas' => [
                    'Saluda / Se despide',
                    'Script establecido',
                ],
            ],
            [
                'seccion' => 'Escucha activa',
                'preguntas' => [
                    'Desconcentración',
                    'Evita espacios en Blanco',
                    'Interrupciones',
                ],
            ],
            [
                'seccion' => 'Fórmulas de Cortesía',
                'preguntas' => [
                    'Personaliza la llamada',
                    'Seguridad en la llamada',
                    'Amabilidad y empatía',
                    'Buen tono de voz/vocabulario/tecnicismos',
                ],
            ],
        ];

        // Inicializar respuestas vacías
        foreach ($this->preguntasPaso3 as $section) {
            foreach ($section['preguntas'] as $q) {
                $this->respuestasPaso3[$q] = '';
            }
        }

        // Inicializar preguntas y respuestas del Paso 5 (PEC-UF)
        $this->preguntasPaso4 = [
            [
                'seccion' => 'Información correcta/completa del producto ofrecido',
                'preguntas' => [
                    'Información correcta/completa del producto ofrecido',
                ],
            ],
            [
                'seccion' => 'PROCESO',
                'preguntas' => [
                    'Correcto proceso de coordinación',
                    'Verificación de recepción de documentos',
                    'Cumple con reglas ortográficas y signos en la redacción',
                    'Derivación innecesaria a Cac',
                ],
            ],
            [
                'seccion' => 'Actitud del servicio',
                'preguntas' => [
                    'Mantiene la atención del cliente en la llamada',
                    'Llamada incompleta/corte de llamada',
                    'Canal abierto',
                ],
            ],
            [
                'seccion' => 'Calidad de atención',
                'preguntas' => [
                    'Solicita y agradece la Espera',
                    'Tiempo de Espera (1-15)',
                    'Responde dentro del tiempo estipulado (0,5 segundos aplica en la campaña whatsapp)',
                ],
            ],
        ];

        foreach ($this->preguntasPaso4 as $section) {
            foreach ($section['preguntas'] as $q) {
                $this->respuestasPaso4[$q] = '';
            }
        }

        // Inicializar preguntas y respuestas del Paso 6 (PEC-NEG)
        $this->preguntasPaso5 = [
            [
                'seccion' => 'Gestión Comercial',
                'preguntas' => [
                    'Seguimiento de Gestión',
                    'Envío de arte',
                    'Validación de datos',
                    'Revisa el cupo precalificado',
                    'Valida correctamente cobertura',
                    'Sondea correctamente necesidad',
                    'Ofrecimiento acorde a la necesidad/escalonada',
                    'Rebate objeciones',
                    'Despeja dudas del producto ofertado',
                    'Ofrecimiento de promoción vigente/objetivo',
                    'Incentiva a la baja',
                    'Procedimiento URL (registro de datos)',
                    'Correcta tipificación del código de conclusión',
                ],
            ],
            [
                'seccion' => 'Validaciones y Registros en CRM (solo en ventas)',
                'preguntas' => [
                    'Registro correcto y completo en en crm ventas',
                ],
            ],
        ];

        foreach ($this->preguntasPaso5 as $section) {
            foreach ($section['preguntas'] as $q) {
                $this->respuestasPaso5[$q] = '';
            }
        }

        // Inicializar preguntas y respuestas del Paso 7 (Manejo de información confidencial)
        $this->preguntasPaso6 = [
            [
                'seccion' => 'Manejo de información confidencial',
                'preguntas' => [
                    'Valida identidad para entregar información',
                    'Resumen completo de venta',
                    'Confirma aceptación del cliente',
                    'Indica que llamada está siendo grabada',
                    'Tratamiento de datos personales (Biométrico)',
                    'Pausa segura',
                    'Solicita permiso para dar información comercial',
                    'Realiza llamada de confirmación de ventas (campaña whatsapp)',
                ],
            ],
        ];

        foreach ($this->preguntasPaso6 as $section) {
            foreach ($section['preguntas'] as $q) {
                $this->respuestasPaso6[$q] = '';
            }
        }

        // Si se proporciona idCic como parámetro o desde sesión flash, cargar datos automáticamente
        $idCicToLoad = $idCic ?? session('idCicManual');

        if ($idCicToLoad) {
            $this->cargarLlamadaPorId($idCicToLoad);
            $this->paso = 1; // Ir directamente al formulario
        }
    }

    public function seleccionarAleatorio()
    {
        \Illuminate\Support\Facades\Log::info('seleccionarAleatorio called (Prepago Digital)');

        // Validar fecha si fue ingresada
        if (!empty($this->filtroFecha)) {
            $this->validarFecha();
            if ($this->getErrorBag()->has('filtroFecha')) {
                return;
            }
        }

        $this->cargarLlamadaAleatoria();
        $this->paso = 1;
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
        // Obtener IDs ya auditados
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

        // Query para llamada aleatoria - SOLO CAMPAÑAS PREPAGO DIGITAL
        $query = DB::table('dbo.Reporte_Llamadas_Detalle')
            ->where('Fecha', '>=', '2025-10-01')
            ->where('Duracion', '>=', 30)
            ->where(function ($query) {
                $query->where('Campaña_Agente', 'LIKE', '%Prepago Digital%')
                    ->orWhere('Campaña_Agente', 'LIKE', '%prepago digital%')
                    ->orWhere('Campaña_Agente', 'LIKE', '%PREPAGO DIGITAL%')
                    ->orWhere('Campaña_Agente', 'LIKE', '%Digital%');
            });

        // Filtro por campaña específica (opcional)
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

        // Excluir IDs ya auditados
        if (!empty($idsAuditados)) {
            $query->whereNotIn('ID_Largo', $idsAuditados);
        }

        $llamada = $query->inRandomOrder()->first();

        if ($llamada) {
            $this->idCic = $llamada->ID_Largo;
            $this->telefono = $llamada->Numero;
            $this->fechaLlamada = $llamada->Fecha;
            $this->fechaMonitoreo = date('Y-m-d');
            $this->duracionSegundos = $llamada->Duracion;
            $this->duracionFormato = $this->formatDuration($llamada->Duracion);
            $this->nombreAsesor = $llamada->NombreCompletoAgente;
            $this->usuarioAsesor = $llamada->Usuario_Llamada_Origen;
            $this->campana = $llamada->Campaña_Agente ?? 'N/A';
        } else {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => 'Sin resultados',
                'text' => 'No hay llamadas de Prepago Digital disponibles con los filtros seleccionados'
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

    public function siguiente()
    {
        // Validar Paso 1 antes de avanzar
        if ($this->paso === 1) {
            $this->validate([
                'tipoGestion' => 'required',
                'campanasDigital' => 'required',
            ], [
                'tipoGestion.required' => 'Debes seleccionar un Tipo de Gestión',
                'campanasDigital.required' => 'Debes seleccionar una Campaña Digital',
            ]);
        }

        // Validar Paso 2 antes de avanzar
        if ($this->paso === 2) {
            $this->validate([
                'tipoMonitoreo' => 'required',
            ], [
                'tipoMonitoreo.required' => 'Debes seleccionar un Tipo de Monitoreo',
            ]);
        }

        // Validar que Paso 3 esté completo antes de avanzar
        if ($this->paso === 3) {
            $rules = [
                'productoOfertadoFijo' => 'required',
                'productoOfertadoMovil' => 'required',
            ];

            $messages = [
                'productoOfertadoFijo.required' => 'Debes seleccionar un Producto Ofertado Fijo',
                'productoOfertadoMovil.required' => 'Debes seleccionar un Producto Ofertado Móvil',
            ];

            // Si seleccionó "Auditoría", validar el campo de detalle
            if ($this->tipoMonitoreo === 'Auditoría') {
                $rules['detalleAuditoria'] = 'required|string|min:10';
                $messages['detalleAuditoria.required'] = 'Debes detallar la auditoría realizada';
                $messages['detalleAuditoria.min'] = 'El detalle debe tener al menos 10 caracteres';
            }

            $this->validate($rules, $messages);
        }

        // Validar que Paso 4 esté completo antes de avanzar
        if ($this->paso === 4) {
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

        // Validar que Paso 5 esté completo antes de avanzar
        if ($this->paso === 5) {
            // Debug: ver qué respuestas tenemos
            \Log::info('Paso 5 - Respuestas:', $this->respuestasPaso4);

            foreach ($this->preguntasPaso4 as $section) {
                foreach ($section['preguntas'] as $pregunta) {
                    $resp = $this->respuestasPaso4[$pregunta] ?? null;
                    if (empty($resp)) {
                        \Log::info('Paso 5 - Falta respuesta para:', ['pregunta' => $pregunta]);
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

        // Validar que Paso 6 esté completo antes de avanzar
        if ($this->paso === 6) {
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

        // Validar que Paso 7 esté completo antes de avanzar
        if ($this->paso === 7) {
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

        // Validar que Paso 8 esté completo antes de guardar
        if ($this->paso === 8) {
            // Validar que se haya seleccionado derivación whatsapp
            if (empty($this->derivacionWhatsapp)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe seleccionar una opción de Derivación WhatsApp'
                ]);
                return;
            }
            // Novedades críticas es opcional, no se valida
        }

        // Validar y procesar Paso 9 (Concretó la venta)
        if ($this->paso === 9) {
            // Validar que se haya seleccionado una opción
            if (empty($this->concretoVenta)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe seleccionar si se concretó la venta'
                ]);
                return;
            }

            // Si concretó venta = SI o NO APLICA, continuar al Paso 10 (Instalación) - Rama SI
            if ($this->concretoVenta === 'SI' || $this->concretoVenta === 'NO APLICA') {
                $this->ramaFlujo = 'venta_si';
                $this->paso = 10;
                $this->bloquearRandomizar = true;
                return;
            }

            // Si concretó venta = NO, continuar al Paso 10 (Causa Raíz) - Rama NO
            if ($this->concretoVenta === 'NO') {
                $this->ramaFlujo = 'venta_no';
                $this->paso = 10;
                $this->bloquearRandomizar = true;
                return;
            }
        }

        // Validar Paso 10 según la rama
        if ($this->paso === 10) {
            // Rama SI: Validar instalación y seguimiento
            if ($this->ramaFlujo === 'venta_si') {
                // Validar preguntas de instalación
                if (empty($this->seInstaloServicio)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe responder: ¿SE INSTALÓ EL SERVICIO?'
                    ]);
                    return;
                }
                if (empty($this->seEntregoEquipoChip)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe responder: ¿SE ENTREGÓ EQUIPO O CHIP?'
                    ]);
                    return;
                }
                if (empty($this->seActivoChip)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe responder: ¿SE ACTIVÓ EL CHIP?'
                    ]);
                    return;
                }
                if (empty($this->seEntregoEquipoMovil)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe responder: SE ENTREGÓ EL EQUIPO MÓVIL?'
                    ]);
                    return;
                }
                if (empty($this->seEntregoEquipoHogar)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe responder: SE ENTREGÓ EQUIPO HOGAR?'
                    ]);
                    return;
                }

                // Validar campos de texto
                if (empty(trim($this->responsableEntrega))) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe ingresar el Responsable de Entrega'
                    ]);
                    return;
                }
                if (empty(trim($this->razonNoEntrega))) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe ingresar la razón de no entrega'
                    ]);
                    return;
                }

                // Validar preguntas de seguimiento
                if (empty($this->asesorRealizoSeguimiento)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe responder: Asesor realizó Seguimiento?'
                    ]);
                    return;
                }
                if (empty($this->ventaRecuperada)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe responder: Venta fue recuperada?'
                    ]);
                    return;
                }
                if (empty($this->solicitudMesesAnteriores)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe responder: Solicitó fue ingresada en meses anteriores'
                    ]);
                    return;
                }
                if (empty($this->solicitudCerradaOtroCanal)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe responder: Solicitud fue cerrado en otro canal por falta de seguimiento'
                    ]);
                    return;
                }
                if (empty($this->ventaNoContacto)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe responder: Venta no se concreta porque el asesor no le volvió a contactar'
                    ]);
                    return;
                }

                // Avanzar al Paso 11 (Observaciones PostVenta)
                $this->paso = 11;
                $this->bloquearRandomizar = true;
                return;
            } // Cerrar rama SI

            // Rama NO: Validar Causa Raíz Principal y avanzar a Paso 11
            if ($this->ramaFlujo === 'venta_no') {
                if (empty($this->causaRaizPrincipal)) {
                    $this->dispatch('showAlert', [
                        'type' => 'warning',
                        'title' => 'Campo Requerido',
                        'text' => 'Debe seleccionar la Causa Raíz Principal'
                    ]);
                    return;
                }

                // Avanzar al Paso 11 (Detalles + Instalación + Seguimiento)
                $this->paso = 11;
                $this->bloquearRandomizar = true;
                return;
            }
        } // Cerrar Paso 10

        // Validar Paso 11 rama NO (Detalles + Instalación + Seguimiento)
        if ($this->paso === 11 && $this->ramaFlujo === 'venta_no') {
            // Validar campo de texto dinámico
            if (empty(trim($this->detallesCausaRaiz))) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe ingresar los detalles de ' . $this->causaRaizPrincipal
                ]);
                return;
            }

            // Validar preguntas de instalación (mismas propiedades que rama SI)
            if (empty($this->seInstaloServicio)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: ¿SE INSTALÓ EL SERVICIO?'
                ]);
                return;
            }
            if (empty($this->seEntregoEquipoChip)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: ¿SE ENTREGÓ EQUIPO O CHIP?'
                ]);
                return;
            }
            if (empty($this->seActivoChip)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: ¿SE ACTIVÓ EL CHIP?'
                ]);
                return;
            }
            if (empty($this->seEntregoEquipoMovil)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: SE ENTREGÓ EL EQUIPO MÓVIL?'
                ]);
                return;
            }
            if (empty($this->seEntregoEquipoHogar)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: SE ENTREGÓ EQUIPO HOGAR?'
                ]);
                return;
            }

            // Validar campos de texto
            if (empty(trim($this->responsableEntrega))) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe ingresar el Responsable de Entrega'
                ]);
                return;
            }
            if (empty(trim($this->razonNoEntrega))) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe ingresar la razón de no entrega'
                ]);
                return;
            }

            // Validar preguntas de seguimiento
            if (empty($this->asesorRealizoSeguimiento)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Asesor realizó Seguimiento?'
                ]);
                return;
            }
            if (empty($this->ventaRecuperada)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Venta fue recuperada?'
                ]);
                return;
            }
            if (empty($this->solicitudMesesAnteriores)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Solicitó fue ingresada en meses anteriores'
                ]);
                return;
            }
            if (empty($this->solicitudCerradaOtroCanal)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Solicitud fue cerrado en otro canal por falta de seguimiento'
                ]);
                return;
            }
            if (empty($this->ventaNoContacto)) {
                $this->dispatch('showAlert', [
                    'type' => 'warning',
                    'title' => 'Campo Requerido',
                    'text' => 'Debe responder: Venta no se concreta porque el asesor no le volvió a contactar'
                ]);
                return;
            }

            // Avanzar al Paso 12 (Observaciones finales)
            $this->paso = 12;
            $this->bloquearRandomizar = true;
            return;
        }

        $this->paso++;
        $this->bloquearRandomizar = true;
    }

    public function retroceder()
    {
        if ($this->paso > 0) {
            // Si estamos en paso 9 de cualquier rama, volver a paso 8
            if ($this->paso === 9 && ($this->ramaFlujo === 'venta_si' || $this->ramaFlujo === 'venta_no')) {
                $this->paso = 8;
                $this->ramaFlujo = ''; // Resetear rama
            } else {
                $this->paso--;
            }
        }
    }

    private function convertirRespuesta($valor)
    {
        // Convertir respuestas de radio buttons a números
        if ($valor === 'SI')
            return '1';
        if ($valor === 'NO')
            return '2';
        if ($valor === 'NO APLICA')
            return '3';
        // Devolver texto tal cual
        return $valor;
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
            // Crear auditoría
            $auditoriaId = DB::table('raz_auditorias_calidad')->insertGetId([
                'formulario_id' => 4,
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
                ]),
                'completada_at' => DB::raw('GETDATE()'),
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);

            // Obtener todas las preguntas del formulario
            $preguntas = DB::table('raz_preguntas_auditorias')
                ->where('formulario_id', 4)
                ->orderBy('orden')
                ->get()
                ->keyBy('texto');

            // PASO 1: Datos de la Llamada
            $respuestasPaso1 = [
                'Nombre del Analista' => $this->nombreAnalista,
                'ID de Interacción (CIC)' => $this->idCic,
                'Teléfono' => $this->telefono,
                'Fecha de Llamada' => $this->fechaLlamada,
                'Fecha de Monitoreo' => $this->fechaMonitoreo,
                'Duración de Llamada' => $this->duracionFormato,
                'Nombre del Asesor' => $this->nombreAsesor,
                'Usuario del Asesor' => $this->usuarioAsesor,
                'Campaña' => $this->campana,
                'Tipo de Gestión' => $this->tipoGestion,
                'Campañas Digital' => $this->campanasDigital,
            ];

            foreach ($respuestasPaso1 as $texto => $valor) {
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

            // PASO 2: Tipo de Monitoreo
            if (isset($preguntas['Tipo de Monitoreo']) && !empty($this->tipoMonitoreo)) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntas['Tipo de Monitoreo']->id,
                    'valor' => $this->tipoMonitoreo,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // PASO 3: Productos Ofertados
            // Detallar auditoría (condicional)
            if (isset($preguntas['Detallar la auditoría realizada y el porqué']) && !empty($this->detalleAuditoria)) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntas['Detallar la auditoría realizada y el porqué']->id,
                    'valor' => $this->detalleAuditoria,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Producto Ofertado Fijo
            if (isset($preguntas['Producto Ofertado Fijo']) && !empty($this->productoOfertadoFijo)) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntas['Producto Ofertado Fijo']->id,
                    'valor' => $this->productoOfertadoFijo,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // Producto Ofertado Móvil
            if (isset($preguntas['Producto Ofertado Móvil']) && !empty($this->productoOfertadoMovil)) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntas['Producto Ofertado Móvil']->id,
                    'valor' => $this->productoOfertadoMovil,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // PASO 4, 5, 6, 7: Respuestas de evaluación
            foreach ($this->respuestasPaso3 as $texto => $valor) {
                if (isset($preguntas[$texto]) && !empty($valor)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas[$texto]->id,
                        'valor' => $this->convertirRespuesta($valor),
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
                        'valor' => $this->convertirRespuesta($valor),
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
                        'valor' => $this->convertirRespuesta($valor),
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
                        'valor' => $this->convertirRespuesta($valor),
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            // PASO 8: Novedades Críticas
            if (isset($preguntas['Novedades Críticas']) && !empty($this->novedadesCriticas)) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntas['Novedades Críticas']->id,
                    'valor' => $this->novedadesCriticas,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // PASO 9: Concretó Venta
            if (isset($preguntas['Concretó Venta']) && !empty($this->concretoVenta)) {
                DB::table('raz_respuestas_auditorias')->insert([
                    'auditoria_id' => $auditoriaId,
                    'pregunta_id' => $preguntas['Concretó Venta']->id,
                    'valor' => $this->concretoVenta,
                    'created_at' => DB::raw('GETDATE()'),
                    'updated_at' => DB::raw('GETDATE()'),
                ]);
            }

            // RAMA SI o NO APLICA
            if ($this->concretoVenta === 'SI' || $this->concretoVenta === 'NO APLICA') {
                // Instalación (Rama SI)
                $instalacionSI = [
                    'SE INSTALÓ EL SERVICIO' => $this->seInstaloServicio,
                    'SE ENTREGÓ EQUIPO O CHIP' => $this->seEntregoEquipoChip,
                    'SE ACTIVÓ EL CHIP' => $this->seActivoChip,
                    'SE ENTREGÓ EL EQUIPO MÓVIL' => $this->seEntregoEquipoMovil,
                    'SE ENTREGÓ EQUIPO HOGAR' => $this->seEntregoEquipoHogar,
                ];

                foreach ($instalacionSI as $texto => $valor) {
                    if (isset($preguntas[$texto]) && !empty($valor)) {
                        DB::table('raz_respuestas_auditorias')->insert([
                            'auditoria_id' => $auditoriaId,
                            'pregunta_id' => $preguntas[$texto]->id,
                            'valor' => $this->convertirRespuesta($valor),
                            'created_at' => DB::raw('GETDATE()'),
                            'updated_at' => DB::raw('GETDATE()'),
                        ]);
                    }
                }

                // Campos de texto
                if (isset($preguntas['Responsable de Entrega']) && !empty($this->responsableEntrega)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas['Responsable de Entrega']->id,
                        'valor' => $this->responsableEntrega,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }

                if (isset($preguntas['Razón no entrega']) && !empty($this->razonNoEntrega)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas['Razón no entrega']->id,
                        'valor' => $this->razonNoEntrega,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }

                // Seguimiento (Rama SI)
                $seguimientoSI = [
                    'Asesor realizó Seguimiento' => $this->asesorRealizoSeguimiento,
                    'Venta fue recuperada' => $this->ventaRecuperada,
                    'Solicitud meses anteriores' => $this->solicitudMesesAnteriores,
                    'Solicitud cerrada otro canal' => $this->solicitudCerradaOtroCanal,
                    'Venta no contacto' => $this->ventaNoContacto,
                ];

                foreach ($seguimientoSI as $texto => $valor) {
                    if (isset($preguntas[$texto]) && !empty($valor)) {
                        DB::table('raz_respuestas_auditorias')->insert([
                            'auditoria_id' => $auditoriaId,
                            'pregunta_id' => $preguntas[$texto]->id,
                            'valor' => $this->convertirRespuesta($valor),
                            'created_at' => DB::raw('GETDATE()'),
                            'updated_at' => DB::raw('GETDATE()'),
                        ]);
                    }
                }

                // Observaciones PostVenta (Rama SI)
                if (isset($preguntas['Observaciones PostVenta']) && !empty($this->observacionesPostVenta)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas['Observaciones PostVenta']->id,
                        'valor' => $this->observacionesPostVenta,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            // RAMA NO
            if ($this->concretoVenta === 'NO') {
                // Causa Raíz Principal
                if (isset($preguntas['Causa Raíz Principal']) && !empty($this->causaRaizPrincipal)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas['Causa Raíz Principal']->id,
                        'valor' => $this->causaRaizPrincipal,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }

                // Detalles Causa Raíz
                if (isset($preguntas['Detalles Causa Raíz']) && !empty($this->detallesCausaRaiz)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas['Detalles Causa Raíz']->id,
                        'valor' => $this->detallesCausaRaiz,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }

                // Instalación (Rama NO) - con prefijo P10B
                $instalacionNO = [
                    'P10B SE INSTALÓ SERVICIO' => $this->seInstaloServicio,
                    'P10B SE ENTREGÓ EQUIPO CHIP' => $this->seEntregoEquipoChip,
                    'P10B SE ACTIVÓ CHIP' => $this->seActivoChip,
                    'P10B SE ENTREGÓ EQUIPO MÓVIL' => $this->seEntregoEquipoMovil,
                    'P10B SE ENTREGÓ EQUIPO HOGAR' => $this->seEntregoEquipoHogar,
                ];

                foreach ($instalacionNO as $texto => $valor) {
                    if (isset($preguntas[$texto]) && !empty($valor)) {
                        DB::table('raz_respuestas_auditorias')->insert([
                            'auditoria_id' => $auditoriaId,
                            'pregunta_id' => $preguntas[$texto]->id,
                            'valor' => $this->convertirRespuesta($valor),
                            'created_at' => DB::raw('GETDATE()'),
                            'updated_at' => DB::raw('GETDATE()'),
                        ]);
                    }
                }

                // Campos de texto (Rama NO)
                if (isset($preguntas['P10B Responsable Entrega']) && !empty($this->responsableEntrega)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas['P10B Responsable Entrega']->id,
                        'valor' => $this->responsableEntrega,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }

                if (isset($preguntas['P10B Razón no entrega']) && !empty($this->razonNoEntrega)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas['P10B Razón no entrega']->id,
                        'valor' => $this->razonNoEntrega,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }

                // Seguimiento (Rama NO) - con prefijo P10B
                $seguimientoNO = [
                    'P10B Asesor Seguimiento' => $this->asesorRealizoSeguimiento,
                    'P10B Venta recuperada' => $this->ventaRecuperada,
                    'P10B Solicitud anterior' => $this->solicitudMesesAnteriores,
                    'P10B Solicitud cerrada otro canal' => $this->solicitudCerradaOtroCanal,
                    'P10B Venta no contacto' => $this->ventaNoContacto,
                ];

                foreach ($seguimientoNO as $texto => $valor) {
                    if (isset($preguntas[$texto]) && !empty($valor)) {
                        DB::table('raz_respuestas_auditorias')->insert([
                            'auditoria_id' => $auditoriaId,
                            'pregunta_id' => $preguntas[$texto]->id,
                            'valor' => $this->convertirRespuesta($valor),
                            'created_at' => DB::raw('GETDATE()'),
                            'updated_at' => DB::raw('GETDATE()'),
                        ]);
                    }
                }

                // Observaciones PostVenta Final (Rama NO)
                if (isset($preguntas['Observaciones PostVenta Final']) && !empty($this->observacionesPostVentaFinal)) {
                    DB::table('raz_respuestas_auditorias')->insert([
                        'auditoria_id' => $auditoriaId,
                        'pregunta_id' => $preguntas['Observaciones PostVenta Final']->id,
                        'valor' => $this->observacionesPostVentaFinal,
                        'created_at' => DB::raw('GETDATE()'),
                        'updated_at' => DB::raw('GETDATE()'),
                    ]);
                }
            }

            $this->auditoriaId = $auditoriaId;
            $this->guardado = true;
            $this->paso = 99;

            $this->dispatch('showAlert', [
                'type' => 'success',
                'title' => '¡Auditoría Guardada!',
                'text' => 'La auditoría de Prepago Digital se guardó exitosamente'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al guardar los datos: ' . $e->getMessage()
            ]);
        }
    }

    public function resetear()
    {
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
        $this->tipoGestion = '';
        $this->tipoMonitoreo = '';
        $this->productoOfertado = '';
        $this->comentarios = '';
        $this->acciones = '';
        $this->respuestasPaso3 = array_fill_keys(array_map(function ($s) {
            return $s;
        }, array_reduce($this->preguntasPaso3, function ($carry, $section) {
            return array_merge($carry, $section['preguntas']);
        }, [])), '');
        $this->respuestasPaso4 = array_fill_keys(array_map(function ($s) {
            return $s;
        }, array_reduce($this->preguntasPaso4, function ($carry, $section) {
            return array_merge($carry, $section['preguntas']);
        }, [])), '');
        $this->respuestasPaso5 = array_fill_keys(array_map(function ($s) {
            return $s;
        }, array_reduce($this->preguntasPaso5, function ($carry, $section) {
            return array_merge($carry, $section['preguntas']);
        }, [])), '');
        $this->respuestasPaso6 = array_fill_keys(array_map(function ($s) {
            return $s;
        }, array_reduce($this->preguntasPaso6, function ($carry, $section) {
            return array_merge($carry, $section['preguntas']);
        }, [])), '');
        $this->novedadesCriticas = '';
        $this->concretoVenta = '';
        $this->seInstaloServicio = '';
        $this->seEntregoEquipoChip = '';
        $this->seActivoChip = '';
        $this->responsableEntrega = '';
        $this->razonNoEntrega = '';
        $this->asesorRealizoSeguimiento = '';
        $this->ventaRecuperada = '';
        $this->solicitudMesesAnteriores = '';
        $this->observacionesPostVenta = '';
        $this->causaRaizPrincipal = '';
        $this->detallesCausaRaiz = '';
        $this->p10_seInstaloServicio = '';
        $this->p10_seEntregoEquipoChip = '';
        $this->p10_seActivoChip = '';
        $this->p10_responsableEntrega = '';
        $this->p10_razonNoEntrega = '';
        $this->p10_asesorRealizoSeguimiento = '';
        $this->p10_ventaRecuperada = '';
        $this->p10_solicitudMesesAnteriores = '';
        $this->observacionesPostVentaFinal = '';
        $this->ramaFlujo = '';

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

    public function getProgressProperty()
    {
        if ($this->paso === 99) {
            return 100;
        }
        // Calcular progreso basado en 8 pasos antes del final
        return min(round(($this->paso / 8) * 100), 95);
    }

    public function render()
    {
        return view('livewire.formulario-digital', [
            'progress' => $this->progress
        ])->layout('components.layouts.app');
    }
}
