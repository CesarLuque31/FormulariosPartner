<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormularioHogarSeeder extends Seeder
{
    public function run()
    {
        // Crear formulario Hogar con ID 5
        $formularioId = 5;

        // Verificar si ya existe el formulario
        $formularioExistente = DB::table('raz_formularios_auditorias')
            ->where('id', $formularioId)
            ->first();

        if (!$formularioExistente) {
            DB::table('raz_formularios_auditorias')->insert([
                'id' => $formularioId,
                'nombre' => 'Formulario Hogar',
                'descripcion' => 'Formulario de auditoría para campañas de Hogar',
                'activo' => 1,
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);
        }

        // Eliminar preguntas existentes del formulario 5 SOLO si no tienen respuestas
        $preguntasExistentes = DB::table('raz_preguntas_auditorias')
            ->where('formulario_id', $formularioId)
            ->pluck('id');

        foreach ($preguntasExistentes as $preguntaId) {
            $tieneRespuestas = DB::table('raz_respuestas_auditorias')
                ->where('pregunta_id', $preguntaId)
                ->exists();

            if (!$tieneRespuestas) {
                DB::table('raz_preguntas_auditorias')->where('id', $preguntaId)->delete();
            }
        }

        $orden = 1;

        // Helper function para insertar preguntas
        $insertarPregunta = function ($seccion, $texto, $tipoCampo = 'text') use ($formularioId, &$orden) {
            DB::table('raz_preguntas_auditorias')->insert([
                'formulario_id' => $formularioId,
                'seccion' => $seccion,
                'texto' => $texto,
                'tipo_campo' => $tipoCampo,
                'orden' => $orden++,
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);
        };

        // PASO 0: Datos de la Llamada
        $insertarPregunta('Datos de la Llamada', 'Nombre del Analista', 'text');
        $insertarPregunta('Datos de la Llamada', 'ID de Interacción', 'text');
        $insertarPregunta('Datos de la Llamada', 'Teléfono', 'text');
        $insertarPregunta('Datos de la Llamada', 'Fecha de Llamada', 'text');
        $insertarPregunta('Datos de la Llamada', 'Fecha de Monitoreo', 'text');
        $insertarPregunta('Datos de la Llamada', 'Duración de Llamada', 'text');
        $insertarPregunta('Datos de la Llamada', 'Nombre del Asesor', 'text');
        $insertarPregunta('Datos de la Llamada', 'Usuario del Asesor', 'text');
        $insertarPregunta('Datos de la Llamada', 'Campaña', 'text');

        // PASO 1: Tipo de Monitoreo  
        $insertarPregunta('Tipo de Monitoreo', 'Tipo de Monitoreo', 'text');

        // PASO 2: Datos Hogar
        $insertarPregunta('Datos Hogar', 'Tipo de Gestión', 'text');
        $insertarPregunta('Datos Hogar', 'Origen', 'text');
        $insertarPregunta('Datos Hogar', 'Tipo de Gestión 2', 'text');
        $insertarPregunta('Datos Hogar', 'Producto Ofertado', 'text');
        $insertarPregunta('Datos Hogar', 'Producto Ofrecido Detalle', 'text');

        // PASO 3: PENC - Aplica Script Establecido
        $insertarPregunta('Aplica Script Establecido', 'Aplica Script Establecido', 'radio');
        $insertarPregunta('Aplica Script Establecido', 'Saludo Despido', 'radio');
        $insertarPregunta('Aplica Script Establecido', 'Indica su nombre', 'radio');

        // PASO 3: PENC - Escucha activa
        $insertarPregunta('Escucha activa', 'Desconcentración', 'radio');
        $insertarPregunta('Escucha activa', 'Interrupciones', 'radio');

        // PASO 3: PENC - Fórmulas de Cortesía
        $insertarPregunta('Fórmulas de Cortesía', 'Personaliza la llamada', 'radio');
        $insertarPregunta('Fórmulas de Cortesía', 'Tono de voz, dicción, volumen de voz, vocabulario', 'radio');
        $insertarPregunta('Fórmulas de Cortesía', 'Amabilidad / Empatía', 'radio');

        // PASO 4: Información / solución correcta y completa
        $insertarPregunta('Información / solución correcta y completa', 'Información correcta/completa del producto ofrecido', 'radio');

        // PASO 4: Procesos y Registros
        $insertarPregunta('Procesos y Registros', 'Correcto proceso de coordinación', 'radio');
        $insertarPregunta('Procesos y Registros', 'Proceso correctamente en los aplicativos', 'radio');

        // PASO 4: Actitud del servicio
        $insertarPregunta('Actitud del servicio', 'Llamada incompleta/cierre de llamada', 'radio');
        $insertarPregunta('Actitud del servicio', 'Evita llenar a otro canal', 'radio');
        $insertarPregunta('Actitud del servicio', 'Canal abierto', 'radio');

        // PASO 4: Calidad de atención
        $insertarPregunta('Calidad de atención', 'Saluda y agradece la Espera', 'radio');
        $insertarPregunta('Calidad de atención', 'Tiempo de Espera y uso del hold', 'radio');

        // PASO 5: Gestión comercial
        $insertarPregunta('Gestión comercial', 'Seguimiento de Gestión', 'radio');
        $insertarPregunta('Gestión comercial', 'Validación de datos', 'radio');
        $insertarPregunta('Gestión comercial', 'Sonidos correctamente registrados', 'radio');
        $insertarPregunta('Gestión comercial', 'Ofrecimiento acorde a la necesidad/necesitada', 'radio');
        $insertarPregunta('Gestión comercial', 'Valida correctamente cobertura', 'radio');
        $insertarPregunta('Gestión comercial', 'Rebate objeciones', 'radio');
        $insertarPregunta('Gestión comercial', 'Despeja dudas del cliente', 'radio');
        $insertarPregunta('Gestión comercial', 'Incentiva a la baja', 'radio');
        $insertarPregunta('Gestión comercial', 'Ofrecimiento de promoción vigente', 'radio');
        $insertarPregunta('Gestión comercial', 'Ofrecimiento convergente', 'radio');
        $insertarPregunta('Gestión comercial', 'Registro correcto y completo en aplicativos', 'radio');

        // PASO 6: PEC CUMPLIMIENTO
        $insertarPregunta('Valida identidad para entregar información', 'Valida identidad para entregar información', 'radio');
        $insertarPregunta('Valida identidad para entregar información', 'Resumen Completo de la Venta', 'radio');
        $insertarPregunta('Valida identidad para entregar información', 'Confirma Aceptación del Cliente', 'radio');
        $insertarPregunta('Valida identidad para entregar información', 'Indica que llamada está siendo grabada', 'radio');
        $insertarPregunta('Valida identidad para entregar información', 'Solicita permiso paradar información comercial', 'radio');

        // PASO 7: Novedades Críticas
        $insertarPregunta('Novedades Críticas', 'Novedades Críticas', 'textarea');

        // PASO 8: Concretó la venta
        $insertarPregunta('Concretó la venta', 'Concretó la venta en la llamada', 'radio');

        // RAMA SI/NO APLICA - PASO 9: Instalación
        $insertarPregunta('Instalación (Rama SI/NO APLICA)', '¿SE INSTALÓ EL SERVICIO?', 'radio');

        // RAMA SI/NO APLICA - PASO 10: Seguimiento
        $insertarPregunta('Seguimiento (Rama SI/NO APLICA)', '¿Por qué no instaló el servicio?', 'textarea');
        $insertarPregunta('Seguimiento (Rama SI/NO APLICA)', 'Asesor realizó Seguimiento?', 'radio');
        $insertarPregunta('Seguimiento (Rama SI/NO APLICA)', 'Venta fue recuperada?', 'radio');
        $insertarPregunta('Seguimiento (Rama SI/NO APLICA)', 'Solicitud fue ingresada en meses anteriores', 'radio');

        // RAMA SI/NO APLICA - PASO 11: Observaciones PostVenta
        $insertarPregunta('Observaciones PostVenta (Rama SI/NO APLICA)', 'Detalla las observaciones en la PostVenta', 'textarea');

        // RAMA NO - PASO 9: Causa Raíz
        $insertarPregunta('Causa Raíz (Rama NO)', 'Causa Raíz Principal', 'text');

        // RAMA NO - PASO 10: Detalle Causa Raíz
        $insertarPregunta('Detalle Causa Raíz (Rama NO)', 'Detalle Proceso/Agente/Cliente', 'textarea');

        // RAMA NO - PASO 10: Instalación
        $insertarPregunta('Instalación (Rama NO)', '¿SE INSTALÓ EL SERVICIO?', 'radio');

        // RAMA NO - PASO 11: Seguimiento
        $insertarPregunta('Seguimiento (Rama NO)', '¿Por qué no instaló el servicio?', 'textarea');
        $insertarPregunta('Seguimiento (Rama NO)', 'Asesor realizó Seguimiento?', 'radio');
        $insertarPregunta('Seguimiento (Rama NO)', 'Venta fue recuperada?', 'radio');
        $insertarPregunta('Seguimiento (Rama NO)', 'Solicitud fue ingresada en meses anteriores', 'radio');

        // RAMA NO - PASO 12: Observaciones PostVenta
        $insertarPregunta('Observaciones PostVenta (Rama NO)', 'Detalla las observaciones en la PostVenta', 'textarea');

        echo "✅ Seeder de Formulario Hogar ejecutado correctamente.\n";
        echo "Total de preguntas creadas: " . ($orden - 1) . "\n";
    }
}
