<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestorePaso10QuestionsSeeder extends Seeder
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
        (58, 1, 'P10 - Validación Venta', 58, '¿Se instaló el servicio?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (59, 1, 'P10 - Validación Venta', 59, '¿Se entregó el chip y el equipo?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (60, 1, 'P10 - Validación Venta', 60, '¿Se entregó el equipo?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (63, 1, 'P10 - Validación No Aplica', 63, '¿Se instaló el servicio?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (64, 1, 'P10 - Validación No Aplica', 64, '¿Se entregó el chip y el equipo?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE()),
        (65, 1, 'P10 - Validación No Aplica', 65, '¿Se entregó el equipo?', 'radio', '{$opciones}', 1, GETDATE(), GETDATE());
        
        SET IDENTITY_INSERT raz_preguntas_auditorias OFF;
        ";

        DB::unprepared($sql);

        echo "✅ Preguntas del Paso 10 restauradas correctamente (6 preguntas)\n";
    }
}
