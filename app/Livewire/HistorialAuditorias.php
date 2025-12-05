<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class HistorialAuditorias extends Component
{
    public $mostrarModal = false;
    public $auditorias = [];
    public $auditoriaSeleccionada = null;
    public $detallesAuditoria = [];

    protected $listeners = ['openHistorial' => 'abrirModal'];

    public function abrirModal()
    {
        $this->cargarAuditorias();
        $this->mostrarModal = true;
    }

    public function cargarAuditorias()
    {
        $this->auditorias = DB::table('raz_auditorias_calidad')
            ->where('dni_auditor', session('dni'))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($auditoria) {
                $metadata = json_decode($auditoria->metadata, true);
                return [
                    'id' => $auditoria->id,
                    'id_cic' => $metadata['id_cic'] ?? 'N/A',
                    'estado' => $auditoria->estado,
                    'fecha' => \Carbon\Carbon::parse($auditoria->created_at)->format('Y-m-d'),
                ];
            });
    }

    public function verDetalle($auditoriaId)
    {
        $this->auditoriaSeleccionada = $auditoriaId;

        // Obtener el formulario_id de la auditoría
        $auditoria = DB::table('raz_auditorias_calidad')->where('id', $auditoriaId)->first();
        $formularioId = $auditoria->formulario_id ?? 1;

        $mapaSecciones = $this->getMapaSecciones($formularioId);

        // Cargar respuestas de la auditoría
        $this->detallesAuditoria = DB::table('raz_respuestas_auditorias as r')
            ->join('raz_preguntas_auditorias as p', 'r.pregunta_id', '=', 'p.id')
            ->where('r.auditoria_id', $auditoriaId)
            ->select('p.texto as pregunta', 'r.valor as respuesta', 'p.seccion', 'p.opciones')
            ->orderBy('p.orden') // Asumiendo orden global
            ->get()
            ->map(function ($item) use ($mapaSecciones) {
                // Convertir valores de radio buttons (1/2/3) a texto legible
                if ($item->respuesta === '1' || $item->respuesta === 1) {
                    $item->respuesta = 'SI';
                } elseif ($item->respuesta === '2' || $item->respuesta === 2) {
                    $item->respuesta = 'NO';
                } elseif ($item->respuesta === '3' || $item->respuesta === 3) {
                    $item->respuesta = 'NO APLICA';
                } else {
                    // Si no es 1/2/3, intentar decodificar opciones
                    $opciones = json_decode($item->opciones, true);
                    if (is_array($opciones)) {
                        foreach ($opciones as $opcion) {
                            // Comparación flexible para manejar "1" vs 1
                            if ($opcion['value'] == $item->respuesta) {
                                $item->respuesta = $opcion['label'];
                                break;
                            }
                        }
                    }
                }

                // Asignar Paso basado en la sección
                $item->paso = $mapaSecciones[$item->seccion] ?? 'Otros';

                return $item;
            })
            ->groupBy('paso');
    }

    private function getMapaSecciones($formularioId = 1)
    {
        // Si es Crosselling (formulario_id = 3), usar mapeo específico
        if ($formularioId == 3) {
            return [
                'Datos de la Llamada' => 'Paso 1 - Datos de la Llamada',
                'Producto Ofertado' => 'Paso 2 - Producto Ofertado',
                // Paso 3 - PEC-SERV
                'P3 - Saluda Se despide' => 'Paso 3 - PEC-SERV',
                'P3 - Escucha activa' => 'Paso 3 - PEC-SERV',
                'P3 - Fórmulas de Cortesía' => 'Paso 3 - PEC-SERV',
                // Paso 4 - PEC-UF
                'P4 - Información correcta/completa del producto ofrecido' => 'Paso 4 - PEC-UF',
                'P4 - PROCESO' => 'Paso 4 - PEC-UF',
                'P4 - Actitud del servicio' => 'Paso 4 - PEC-UF',
                'P4 - Calidad de atención' => 'Paso 4 - PEC-UF',
                // Paso 5 - PEC-NEG
                'P5 - Gestión Comercial' => 'Paso 5 - PEC-NEG',
                'P5 - Validaciones y Registros en CRM' => 'Paso 5 - PEC-NEG',
                // Paso 6 - PEC-CUM
                'P6 - Manejo de información confidencial' => 'Paso 6 - PEC-CUM',
                'Novedades Críticas' => 'Paso 7 - Novedades Críticas',
                'Concretó Venta' => 'Paso 8 - Concretó Venta',
                'P9A - Instalación' => 'Paso 9 - Instalación del Servicio',
                'P10A - Observaciones' => 'Paso 10 - Observaciones PostVenta',
                'P9B - Causa Raíz' => 'Paso 9 - Causa Raíz Principal',
                'P10B - Detalles' => 'Paso 10 - Detalles Causa Raíz',
                'P11 - Observaciones Final' => 'Paso 11 - Observaciones Final',
            ];
        }

        // Si es Prepago Digital (formulario_id = 4), usar mapeo específico
        if ($formularioId == 4) {
            return [
                'Datos de la Llamada' => 'Paso 1 - Datos de la Llamada',
                'Tipo de Monitoreo' => 'Paso 2 - Tipo de Monitoreo',
                'Productos Ofertados' => 'Paso 3 - Productos Ofertados',
                'P4 - Protocolos y Buenas Prácticas' => 'Paso 4 - Protocolos y Buenas Prácticas',
                'P5 - PEC-UF' => 'Paso 5 - PEC-UF',
                'P6 - PEC-NEG' => 'Paso 6 - PEC-NEG',
                'P7 - Manejo de información confidencial' => 'Paso 7 - Manejo de información confidencial',
                'Novedades Críticas' => 'Paso 8 - Novedades Críticas',
                'Derivación WhatsApp' => 'Paso 8 - Derivación WhatsApp',
                'Concretó Venta' => 'Paso 9 - Concretó Venta',
                'Instalación (Rama SI)' => 'Paso 10 - Instalación del Servicio (SI)',
                'Seguimiento (Rama SI)' => 'Paso 10 - Seguimiento (SI)',
                'Observaciones PostVenta (Rama SI)' => 'Paso 11 - Observaciones PostVenta (SI)',
                'Causa Raíz (Rama NO)' => 'Paso 10 - Causa Raíz Principal (NO)',
                'Detalles Causa Raíz (Rama NO)' => 'Paso 11 - Detalles Causa Raíz (NO)',
                'Instalación (Rama NO)' => 'Paso 11 - Instalación del Servicio (NO)',
                'Seguimiento (Rama NO)' => 'Paso 11 - Seguimiento (NO)',
                'Observaciones PostVenta Final (Rama NO)' => 'Paso 12 - Observaciones Final (NO)',
            ];
        }

        // Si es Hogar (formulario_id = 5), usar mapeo específico
        if ($formularioId == 5) {
            return [
                // Paso 1 del blade = Tipo de Monitoreo
                'Tipo de Monitoreo' => 'Tipo de Monitoreo',
                // Paso 2 del blade = Datos de la Llamada + Datos Hogar
                'Datos de la Llamada' => 'Datos de la Llamada y Datos Hogar',
                'Datos Hogar' => 'Datos de la Llamada y Datos Hogar',
                // Paso 3 del blade = PENC
                'Aplica Script Establecido' => 'PENC - Protocolos y Buenas Prácticas',
                'Escucha activa' => 'PENC - Protocolos y Buenas Prácticas',
                'Fórmulas de Cortesía' => 'PENC - Protocolos y Buenas Prácticas',
                // Paso 4 del blade = Calidad
                'Información / solución correcta y completa' => 'Calidad de Atención',
                'Procesos y Registros' => 'Calidad de Atención',
                'Actitud del servicio' => 'Calidad de Atención',
                'Calidad de atención' => 'Calidad de Atención',
                // Paso 5 del blade = Gestión comercial
                'Gestión comercial' => 'Gestión Comercial',
                // Paso 6 del blade = PEC CUMPLIMIENTO
                'Valida identidad para entregar información' => 'PEC - Cumplimiento',
                // Paso 7 del blade = Novedades
                'Novedades Críticas' => 'Novedades Críticas',
                // Paso 8 del blade = Venta
                'Concretó la venta' => 'Validación de Venta',
                // Paso 9-11 Rama SI/NO APLICA
                'Instalación (Rama SI/NO APLICA)' => 'Instalación del Servicio',
                'Seguimiento (Rama SI/NO APLICA)' => 'Seguimiento PostVenta',
                'Observaciones PostVenta (Rama SI/NO APLICA)' => 'Observaciones PostVenta',
                // Paso 9-12 Rama NO
                'Causa Raíz (Rama NO)' => 'Análisis de Causa Raíz',
                'Detalle Causa Raíz (Rama NO)' => 'Análisis de Causa Raíz',
                'Instalación (Rama NO)' => 'Instalación del Servicio',
                'Seguimiento (Rama NO)' => 'Seguimiento PostVenta',
                'Observaciones PostVenta (Rama NO)' => 'Observaciones Finales',
            ];
        }

        // Mapeo original para formulario de auditoría (formulario_id = 1)
        $secciones = DB::table('raz_preguntas_auditorias')
            ->where('formulario_id', $formularioId)
            ->select('seccion', DB::raw('MIN(id) as min_id'))
            ->groupBy('seccion')
            ->orderBy('min_id')
            ->pluck('seccion')
            ->toArray();

        $total = count($secciones);
        $map = [];

        foreach ($secciones as $index => $seccion) {
            $reverseIndex = $total - $index; // 1 es el último

            if ($index === 0) {
                $map[$seccion] = 'Paso 1 - Datos de la Llamada';
            } elseif ($index === 1) {
                $map[$seccion] = 'Paso 2 - Evaluación Inicial';
            } elseif ($index === 2) {
                $map[$seccion] = 'Paso 3 - Clasificación';
            } elseif ($reverseIndex <= 3) {
                $map[$seccion] = 'Paso 12 - Seguimiento y Cierre';
            } elseif ($reverseIndex <= 8) {
                $map[$seccion] = 'Paso 11 - Análisis de Causa';
            } elseif ($reverseIndex <= 12) {
                $map[$seccion] = 'Sección Inactiva'; // Extra
            } elseif ($reverseIndex <= 15) {
                $map[$seccion] = 'Paso 10 - Validación';
            } elseif ($reverseIndex === 16) {
                $map[$seccion] = 'Paso 9 - Validación Venta';
            } elseif ($reverseIndex === 17) {
                $map[$seccion] = 'Paso 8 - Cierre';
            } elseif ($reverseIndex === 18) {
                $map[$seccion] = 'Paso 7 - Manejo de Objeciones';
            } elseif ($reverseIndex <= 20) {
                $map[$seccion] = 'Paso 6 - Sondeo';
            } elseif ($reverseIndex <= 24) {
                $map[$seccion] = 'Paso 5 - Presentación';
            } else {
                $map[$seccion] = 'Paso 4 - Desarrollo de la Llamada';
            }
        }

        return $map;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->auditoriaSeleccionada = null;
        $this->detallesAuditoria = [];
    }

    public function render()
    {
        return view('livewire.historial-auditorias');
    }
}
