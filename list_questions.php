<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$preguntas = DB::table('raz_preguntas_auditorias')
    ->where('formulario_id', 4)
    ->orderBy('orden')
    ->get(['id', 'texto', 'seccion', 'orden']);

echo "Total preguntas: " . $preguntas->count() . "\n\n";

foreach ($preguntas as $p) {
    echo sprintf("%3d | %-50s | %s\n", $p->orden, $p->texto, $p->seccion);
}
