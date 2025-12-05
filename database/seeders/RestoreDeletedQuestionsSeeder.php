<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestoreDeletedQuestionsSeeder extends Seeder
{
    public function run()
    {
        $opciones = str_replace("'", "''", json_encode([
            ['value' => 'SI', 'label' => 'SI'],
            ['value' => 'NO', 'label' => 'NO'],
            ['value' => 'NO APLICA', 'label' => 'NO APLICA']
        ]));

        $sql = "
        SET IDENTITY_INSERT raz_preguntas_auditorias ON;
        
        INSERT INTO raz_preguntas_auditorias (id, formulario_id, seccion, orden, texto, tipo_campo, opciones, requerido, created_at, updated_at) VALUES
        (57, 1, 'P10 - Validación Venta', 57, '¿Se generó la orden?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (62, 1, 'P10 - Validación No Aplica', 62, '¿Se generó la orden?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (77, 1, 'P11 - Análisis Proceso', 77, '¿Se generó la orden?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (78, 1, 'P11 - Análisis Proceso', 78, '¿Se instaló el servicio?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (79, 1, 'P11 - Análisis Proceso', 79, '¿Se entregó el chip y el equipo?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (80, 1, 'P11 - Análisis Proceso', 80, '¿Se entregó el equipo?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (81, 1, 'P11 - Análisis Proceso', 81, 'Porque no se concreto la venta ?', 'textarea', NULL, 1, GETDATE(), GETDATE()),
        (83, 1, 'P11 - Análisis Agente', 83, '¿Se generó la orden?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (84, 1, 'P11 - Análisis Agente', 84, '¿Se instaló el servicio?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (85, 1, 'P11 - Análisis Agente', 85, '¿Se entregó el chip y el equipo?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (86, 1, 'P11 - Análisis Agente', 86, '¿Se entregó el equipo?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (87, 1, 'P11 - Análisis Agente', 87, 'Porque no se concreto la venta ?', 'textarea', NULL, 1, GETDATE(), GETDATE()),
        (89, 1, 'P11 - Análisis Cliente', 89, '¿Se generó la orden?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (90, 1, 'P11 - Análisis Cliente', 90, '¿Se instaló el servicio?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (91, 1, 'P11 - Análisis Cliente', 91, '¿Se entregó el chip y el equipo?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (92, 1, 'P11 - Análisis Cliente', 92, '¿Se entregó el equipo?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (93, 1, 'P11 - Análisis Cliente', 93, 'Porque no se concreto la venta ?', 'textarea', NULL, 1, GETDATE(), GETDATE());
        
        SET IDENTITY_INSERT raz_preguntas_auditorias OFF;
        ";

        DB::unprepared($sql);

        echo "✅ Todas las preguntas restauradas correctamente (17 preguntas)\n";
    }
}
