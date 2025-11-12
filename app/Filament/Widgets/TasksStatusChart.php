<?php

namespace App\Filament\Widgets;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class TasksStatusChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Distribución de Tareas por Estado';

    protected ?string $description = 'Vista general del estado de todas tus tareas';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $userId = Auth::id();

        // Obtener conteo por cada estado
        $pending = Task::where('user_id', $userId)
            ->where('status', TaskStatus::Pendiente)
            ->count();

        $inProgress = Task::where('user_id', $userId)
            ->where('status', TaskStatus::EnProgreso)
            ->count();

        $completed = Task::where('user_id', $userId)
            ->where('status', TaskStatus::Completado)
            ->count();

        $cancelled = Task::where('user_id', $userId)
            ->where('status', TaskStatus::Cancelada)
            ->count();

        $total = $pending + $inProgress + $completed + $cancelled;

        return [
            'datasets' => [
                [
                    'label' => 'Tareas',
                    'data' => [$pending, $inProgress, $completed, $cancelled],
                    'backgroundColor' => [
                        'rgb(251, 191, 36)', // Amarillo (Pending)
                        'rgb(59, 130, 246)', // Azul (In Progress)
                        'rgb(34, 197, 94)', // Verde (Completed)
                        'rgb(239, 68, 68)', // Rojo (Cancelled)
                    ],
                    'hoverBackgroundColor' => [
                        'rgba(251, 191, 36, 0.8)', // Amarillo más claro
                        'rgba(59, 130, 246, 0.8)', // Azul más claro
                        'rgba(34, 197, 94, 0.8)', // Verde más claro
                        'rgba(239, 68, 68, 0.8)', // Rojo más claro
                    ],
                    'borderColor' => 'rgb(17, 24, 39)', // Color del fondo oscuro
                    'borderWidth' => 3,
                    'hoverBorderWidth' => 1,
                    'hoverBorderColor' => 'rgb(255, 255, 255)',
                    'spacing' => 0,
                ],
            ],
            'labels' => [
                "Pendientes ({$pending})",
                "En Progreso ({$inProgress})",
                "Completadas ({$completed})",
                "Canceladas ({$cancelled})",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'align' => 'center',
                    'labels' => [
                        'boxWidth' => 15,
                        'padding' => 15,
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'padding' => 12,
                    'cornerRadius' => 8,
                ],
            ],
            'maintainAspectRatio' => true,
            'responsive' => true,
            'aspectRatio' => 1.5,
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
                'duration' => 1000,
                'easing' => 'easeInOutQuart',
            ],
            'interaction' => [
                'mode' => 'nearest',
                'intersect' => true,
            ],
            'hover' => [
                'mode' => 'nearest',
                'intersect' => true,
                'animationDuration' => 400,
            ],
            'layout' => [
                'padding' => [
                    'left' => 40,
                    'right' => 40,
                    'top' => 20,
                    'bottom' => 20,
                ],
            ],
        ];
    }
}
