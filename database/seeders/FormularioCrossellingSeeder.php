<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormularioCrossellingSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existe el formulario_id = 3
        $existe = DB::table('raz_formularios_auditorias')->where('id', 3)->exists();

        if (!$existe) {
            DB::table('raz_formularios_auditorias')->insert([
                'id' => 3,
                'nombre' => 'Formulario Crosselling',
                'descripcion' => 'Formulario específico para auditorías de campañas de Crosselling',
                'cargo_ids' => '[]',
                'activo' => 1,
                'created_at' => DB::raw('GETDATE()'),
                'updated_at' => DB::raw('GETDATE()'),
            ]);
            $this->command->info('✅ Formulario Crosselling creado.');
        }

        // Eliminar preguntas existentes
        DB::table('raz_preguntas_auditorias')->where('formulario_id', 3)->delete();

        $orden = 1;
        // Opciones para radio buttons
        $opcionesRadio = [['value' => '1', 'label' => 'SI'], ['value' => '2', 'label' => 'NO'], ['value' => '3', 'label' => 'NO APLICA']];

        // PASO 1: Datos de la Llamada
        $this->insertarPregunta(3, 'Datos de la Llamada', 'Nombre del Analista', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'Datos de la Llamada', 'ID de Interacción (CIC)', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'Datos de la Llamada', 'Teléfono', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'Datos de la Llamada', 'Fecha de Llamada', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'Datos de la Llamada', 'Fecha de Monitoreo', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'Datos de la Llamada', 'Duración de Llamada', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'Datos de la Llamada', 'Nombre del Asesor', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'Datos de la Llamada', 'Usuario del Asesor', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'Datos de la Llamada', 'Campaña', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'Datos de la Llamada', 'Tipo de Gestión', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'Datos de la Llamada', 'Tipo de Monitoreo', 'text', 1, $orden++);

        // PASO 2
        $this->insertarPregunta(3, 'Producto Ofertado', 'Producto Ofertado', 'text', 1, $orden++);

        // PASO 3: PEC-SERV
        $this->insertarPregunta(3, 'P3 - Saluda Se despide', 'Saluda / Se despide', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P3 - Saluda Se despide', 'Script establecido', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P3 - Escucha activa', 'Desconcentración', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P3 - Escucha activa', 'Evita espacios en Blanco', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P3 - Escucha activa', 'Interrupciones', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P3 - Fórmulas de Cortesía', 'Personaliza la llamada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P3 - Fórmulas de Cortesía', 'Seguridad en la llamada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P3 - Fórmulas de Cortesía', 'Amabilidad y empatía', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P3 - Fórmulas de Cortesía', 'Buen tono de voz/vocabulario/tecnicismos', 'radio', 1, $orden++, $opcionesRadio);

        // PASO 4: PEC-UF
        $this->insertarPregunta(3, 'P4 - Información correcta/completa del producto ofrecido', 'Información correcta/completa del producto ofrecido', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P4 - PROCESO', 'Correcto proceso de coordinación', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P4 - PROCESO', 'Verificación de recepción de documentos', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P4 - Actitud del servicio', 'Mantiene la atención del cliente en la llamada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P4 - Actitud del servicio', 'Llamada incompleta/corte de llamada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P4 - Actitud del servicio', 'Canal abierto', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P4 - Calidad de atención', 'Solicita y agradece la Espera', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P4 - Calidad de atención', 'Tiempo de Espera (1:15)', 'radio', 1, $orden++, $opcionesRadio);

        // PASO 5: PEC-NEG
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Seguimiento de Gestión', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Validación de datos', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Valida correctamente cobertura', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Indaga las necesidades del cliente', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Ofrecimiento acorde a la necesidad', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Realiza ofrecimiento comercial de manera escalonada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Rebate objeciones', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Despeja dudas del producto ofertado', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Ofrecimiento de promoción vigente/objetivo', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Incentiva a la baja', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Gestión Comercial', 'Procedimiento URL (registro de datos)', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Validaciones y Registros en CRM', 'Registro correcto y completo en crm ventas', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P5 - Validaciones y Registros en CRM', 'Registro correctamente el codigo de conclusion', 'radio', 1, $orden++, $opcionesRadio);

        // PASO 6: PEC-CUM
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Valida identidad para entregar información', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Resumen completo de venta', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Confirma aceptación del cliente', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Indica que llamada está siendo grabada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Tratamiento de datos personales', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Pausa segura', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Solicita permiso para dar información comercial', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Cumple con el proceso biométrico', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Acepta bancarización', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Da información correcta y completa de permanencia', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P6 - Manejo de información confidencial', 'Menciona condonación de deuda', 'radio', 1, $orden++, $opcionesRadio);

        // PASO 7
        $this->insertarPregunta(3, 'Novedades Críticas', 'Novedades Críticas', 'textarea', 0, $orden++);

        // PASO 8
        $opcionesPaso8 = [['value' => '1', 'label' => 'SI'], ['value' => '2', 'label' => 'NO'], ['value' => '3', 'label' => 'NO APLICA']];
        $this->insertarPregunta(3, 'Concretó Venta', 'Concretó Venta', 'radio', 1, $orden++, $opcionesPaso8);

        // PASO 9A: Instalación (Rama SI/NO APLICA)
        $this->insertarPregunta(3, 'P9A - Instalación', 'SE INSTALÓ EL SERVICIO', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P9A - Instalación', 'SE ENTREGÓ EQUIPO O CHIP', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P9A - Instalación', 'SE ACTIVÓ EL CHIP', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P9A - Instalación', 'Responsable de Entrega', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'P9A - Instalación', 'Razón no entrega', 'textarea', 1, $orden++);
        $this->insertarPregunta(3, 'P9A - Instalación', 'Asesor realizó Seguimiento', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P9A - Instalación', 'Venta fue recuperada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P9A - Instalación', 'Solicitud meses anteriores', 'radio', 1, $orden++, $opcionesRadio);

        // PASO 10A: Observaciones (Rama SI/NO APLICA)
        $this->insertarPregunta(3, 'P10A - Observaciones', 'Observaciones PostVenta', 'textarea', 0, $orden++);

        // PASO 9B: Causa Raíz (Rama NO)
        $opcionesPaso9B = [['value' => 'Proceso', 'label' => 'Proceso'], ['value' => 'Agente', 'label' => 'Agente'], ['value' => 'Cliente', 'label' => 'Cliente']];
        $this->insertarPregunta(3, 'P9B - Causa Raíz', 'Causa Raíz Principal', 'radio', 1, $orden++, $opcionesPaso9B);

        // PASO 10B: Detalles (Rama NO)
        $this->insertarPregunta(3, 'P10B - Detalles', 'Detalles Causa Raíz', 'textarea', 1, $orden++);
        $this->insertarPregunta(3, 'P10B - Detalles', 'P10B SE INSTALÓ SERVICIO', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P10B - Detalles', 'P10B SE ENTREGÓ EQUIPO CHIP', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P10B - Detalles', 'P10B SE ACTIVÓ CHIP', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P10B - Detalles', 'P10B Responsable Entrega', 'text', 1, $orden++);
        $this->insertarPregunta(3, 'P10B - Detalles', 'P10B Razón no entrega', 'textarea', 1, $orden++);
        $this->insertarPregunta(3, 'P10B - Detalles', 'P10B Asesor Seguimiento', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P10B - Detalles', 'P10B Venta recuperada', 'radio', 1, $orden++, $opcionesRadio);
        $this->insertarPregunta(3, 'P10B - Detalles', 'P10B Solicitud anterior', 'radio', 1, $orden++, $opcionesRadio);

        // PASO 11: Observaciones Final (Rama NO)
        $this->insertarPregunta(3, 'P11 - Observaciones Final', 'Observaciones Final', 'textarea', 0, $orden++);

        $this->command->info("✅ {$orden} preguntas creadas para Formulario Crosselling.");
    }

    private function insertarPregunta($formulario_id, $seccion, $texto, $tipo, $requerido, $orden, $opciones = null)
    {
        DB::table('raz_preguntas_auditorias')->insert([
            'formulario_id' => $formulario_id,
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
