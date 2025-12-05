<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormularioDigitalSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existe el formulario_id = 4
        $existe = DB::table('raz_formularios_auditorias')->where('id', 4)->exists();

        if (!$existe) {
            DB::table('raz_formularios_auditorias')->insert([
                'id' => 4,
                'nombre' => 'Formulario Prepago Digital',
                'descripcion' => 'Formulario específico para auditorías de campañas Prepago Digital',
                'cargo_ids' => '[]',
                'activo' => 1,
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);
            $this->command->info('✅ Formulario Prepago Digital creado.');
        }

        // Eliminar preguntas existentes solo si no hay respuestas
        $hayRespuestas = DB::table('raz_respuestas_auditorias')
            ->whereIn('pregunta_id', function ($query) {
                $query->select('id')
                    ->from('raz_preguntas_auditorias')
                    ->where('formulario_id', 4);
            })
            ->exists();

        if (!$hayRespuestas) {
            DB::table('raz_preguntas_auditorias')->where('formulario_id', 4)->delete();
            $this->command->info('🗑️  Preguntas anteriores eliminadas.');
        } else {
            $this->command->info('⚠️  Ya existen respuestas guardadas. No se eliminarán preguntas.');
            return; // No continuar si ya hay datos
        }

        $orden = 1;
        $opcionesRadio = [['value' => '1', 'label' => 'SI'], ['value' => '2', 'label' => 'NO'], ['value' => '3', 'label' => 'NO APLICA']];

        // PASO 1: Datos de la Llamada
        $this->insertarPregunta(4, 'Datos de la Llamada', 'Nombre del Analista', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Datos de la Llamada', 'ID de Interacción (CIC)', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Datos de la Llamada', 'Teléfono', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Datos de la Llamada', 'Fecha de Llamada', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Datos de la Llamada', 'Fecha de Monitoreo', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Datos de la Llamada', 'Duración de Llamada', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Datos de la Llamada', 'Nombre del Asesor', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Datos de la Llamada', 'Usuario del Asesor', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Datos de la Llamada', 'Campaña', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Datos de la Llamada', 'Tipo de Gestión', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Datos de la Llamada', 'Campañas Digital', 'text', 1, $orden++);

        // PASO 2: Tipo de Monitoreo
        $this->insertarPregunta(4, 'Tipo de Monitoreo', 'Tipo de Monitoreo', 'text', 1, $orden++);

        // PASO 3: Productos Ofertados
        $this->insertarPregunta(4, 'Productos Ofertados', 'Detallar la auditoría realizada y el porqué', 'textarea', 0, $orden++);
        $this->insertarPregunta(4, 'Productos Ofertados', 'Producto Ofertado Fijo', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Productos Ofertados', 'Producto Ofertado Móvil', 'text', 1, $orden++);

        // PASO 4: Evaluación - Protocolos y Buenas Prácticas (9 preguntas)
        $this->insertarPregunta(4, 'P4 - Protocolos y Buenas Prácticas', 'Saluda / Se despide', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P4 - Protocolos y Buenas Prácticas', 'Script establecido', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P4 - Protocolos y Buenas Prácticas', 'Desconcentración', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P4 - Protocolos y Buenas Prácticas', 'Evita espacios en Blanco', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P4 - Protocolos y Buenas Prácticas', 'Interrupciones', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P4 - Protocolos y Buenas Prácticas', 'Personaliza la llamada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P4 - Protocolos y Buenas Prácticas', 'Seguridad en la llamada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P4 - Protocolos y Buenas Prácticas', 'Amabilidad y empatía', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P4 - Protocolos y Buenas Prácticas', 'Buen tono de voz/vocabulario/tecnicismos', 'radio', 1, $orden++, $opcionesRadio);

        // PASO 5: PEC-UF (11 preguntas)
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Información correcta/completa del producto ofrecido', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Correcto proceso de coordinación', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Verificación de recepción de documentos', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Cumple con reglas ortográficas y signos en la redacción', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Derivación innecesaria a Cac', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Mantiene la atención del cliente en la llamada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Llamada incompleta/corte de llamada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Canal abierto', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Solicita y agradece la Espera', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Tiempo de Espera (1-15)', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P5 - PEC-UF', 'Responde dentro del tiempo estipulado (0,5 segundos aplica en la campaña whatsapp)', 'radio', 1, $orden++, $opcionesRadio);

        // PASO 6: PEC-NEG (14 preguntas)
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Seguimiento de Gestión', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Envío de arte', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Validación de datos', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Revisa el cupo precalificado', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Valida correctamente cobertura', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Sondea correctamente necesidad', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Ofrecimiento acorde a la necesidad/escalonada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Rebate objeciones', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Despeja dudas del producto ofertado', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Ofrecimiento de promoción vigente/objetivo', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Incentiva a la baja', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Procedimiento URL (registro de datos)', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Correcta tipificación del código de conclusión', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P6 - PEC-NEG', 'Registro correcto y completo en en crm ventas', 'radio', 1, $orden++, $opcionesRadio);

        // PASO 7: Manejo de información confidencial (8 preguntas)
        $this->insertarPregunta(4, 'P7 - Manejo de información confidencial', 'Valida identidad para entregar información', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P7 - Manejo de información confidencial', 'Resumen completo de venta', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P7 - Manejo de información confidencial', 'Confirma aceptación del cliente', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P7 - Manejo de información confidencial', 'Indica que llamada está siendo grabada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P7 - Manejo de información confidencial', 'Tratamiento de datos personales (Biométrico)', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P7 - Manejo de información confidencial', 'Pausa segura', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P7 - Manejo de información confidencial', 'Solicita permiso para dar información comercial', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'P7 - Manejo de información confidencial', 'Realiza llamada de confirmación de ventas (campaña whatsapp)', 'radio', 1, $orden++, $opcionesRadio);

        // PASO 8: Novedades Críticas y Derivación WhatsApp
        $this->insertarPregunta(4, 'Novedades Críticas', 'Novedades Críticas', 'textarea', 0, $orden++);
        $this->insertarPregunta(4, 'Derivación WhatsApp', 'Derivación WhatsApp', 'text', 1, $orden++);

        // PASO 9: Concretó Venta
        $this->insertarPregunta(4, 'Concretó Venta', 'Concretó Venta', 'radio', 1, $orden++, $opcionesRadio);

        // RAMA SI: Paso 10 - Instalación y Seguimiento
        $this->insertarPregunta(4, 'Instalación (Rama SI)', 'SE INSTALÓ EL SERVICIO', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Instalación (Rama SI)', 'SE ENTREGÓ EQUIPO O CHIP', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Instalación (Rama SI)', 'SE ACTIVÓ EL CHIP', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Instalación (Rama SI)', 'SE ENTREGÓ EL EQUIPO MÓVIL', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Instalación (Rama SI)', 'SE ENTREGÓ EQUIPO HOGAR', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Instalación (Rama SI)', 'Responsable de Entrega', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Instalación (Rama SI)', 'Razón no entrega', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Seguimiento (Rama SI)', 'Asesor realizó Seguimiento', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Seguimiento (Rama SI)', 'Venta fue recuperada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Seguimiento (Rama SI)', 'Solicitud meses anteriores', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Seguimiento (Rama SI)', 'Solicitud cerrada otro canal', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Seguimiento (Rama SI)', 'Venta no contacto', 'radio', 1, $orden++, $opcionesRadio);

        // RAMA SI: Paso 11 - Observaciones PostVenta
        $this->insertarPregunta(4, 'Observaciones PostVenta (Rama SI)', 'Observaciones PostVenta', 'textarea', 0, $orden++);

        // RAMA NO: Paso 10 - Causa Raíz Principal
        $this->insertarPregunta(4, 'Causa Raíz (Rama NO)', 'Causa Raíz Principal', 'text', 1, $orden++);

        // RAMA NO: Paso 11 - Detalles + Instalación + Seguimiento
        $this->insertarPregunta(4, 'Detalles Causa Raíz (Rama NO)', 'Detalles Causa Raíz', 'textarea', 1, $orden++);
        $this->insertarPregunta(4, 'Instalación (Rama NO)', 'P10B SE INSTALÓ SERVICIO', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Instalación (Rama NO)', 'P10B SE ENTREGÓ EQUIPO CHIP', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Instalación (Rama NO)', 'P10B SE ACTIVÓ CHIP', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Instalación (Rama NO)', 'P10B SE ENTREGÓ EQUIPO MÓVIL', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Instalación (Rama NO)', 'P10B SE ENTREGÓ EQUIPO HOGAR', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Instalación (Rama NO)', 'P10B Responsable Entrega', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Instalación (Rama NO)', 'P10B Razón no entrega', 'text', 1, $orden++);
        $this->insertarPregunta(4, 'Seguimiento (Rama NO)', 'P10B Asesor Seguimiento', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Seguimiento (Rama NO)', 'P10B Venta recuperada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Seguimiento (Rama NO)', 'P10B Solicitud anterior', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Seguimiento (Rama NO)', 'P10B Solicitud cerrada otro canal', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(4, 'Seguimiento (Rama NO)', 'P10B Venta no contacto', 'radio', 1, $orden++, $opcionesRadio);

        // RAMA NO: Paso 12 - Observaciones PostVenta Final
        $this->insertarPregunta(4, 'Observaciones PostVenta Final (Rama NO)', 'Observaciones PostVenta Final', 'textarea', 0, $orden++);

        $this->command->info('✅ Preguntas del Formulario Prepago Digital creadas exitosamente.');
    }

    private function insertarPregunta($formularioId, $seccion, $texto, $tipo, $requerido, $orden, $opciones = null)
    {
        DB::table('raz_preguntas_auditorias')->insert([
            'formulario_id' => $formularioId,
            'seccion' => $seccion,
            'texto' => $texto,
            'tipo_campo' => $tipo,
            'opciones' => $opciones ? json_encode($opciones) : null,
            'requerido' => $requerido,
            'orden' => $orden,
            'created_at' => DB::raw('GETDATE()'),
            'updated_at' => DB::raw('GETDATE()'),
        ]);
    }
}
