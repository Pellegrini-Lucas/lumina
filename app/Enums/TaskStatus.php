<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatus: string
{
    case Pendiente = 'pendiente';
    case EnProgreso = 'en_progreso';
    case Completado = 'completado';
    case Cancelada = 'cancelada';
}
