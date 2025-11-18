<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;
use Carbon\Carbon;

// Crear eventos de prueba
$events = [
    [
        'user_id' => 1,
        'title' => 'Cierre de mes',
        'description' => 'Reunión de cierre mensual',
        'start_time' => Carbon::parse('2025-11-30 16:00:00'),
        'end_time' => Carbon::parse('2025-11-30 18:00:00'),
        'is_all_day' => false,
    ],
    [
        'user_id' => 1,
        'title' => 'Inicio de diciembre',
        'description' => 'Planificación mes nuevo',
        'start_time' => Carbon::parse('2025-12-01 09:00:00'),
        'end_time' => Carbon::parse('2025-12-01 10:30:00'),
        'is_all_day' => false,
    ],
];

foreach ($events as $eventData) {
    Event::create($eventData);
    echo "✓ Evento creado: {$eventData['title']} - {$eventData['start_time']->format('d/m/Y H:i')}\n";
}

echo "\n¡2 eventos adicionales creados exitosamente!\n";
