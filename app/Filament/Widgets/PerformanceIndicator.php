<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PerformanceIndicator extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $userId = Auth::id();
        $thirtyDaysAgo = now()->subDays(30);

        // Total de tareas con fecha de vencimiento en los últimos 30 días
        $totalTasksWithDueDate = Task::where('user_id', $userId)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', $thirtyDaysAgo)
            ->count();

        // Tareas completadas a tiempo (antes o en la fecha de vencimiento)
        $completedOnTime = Task::where('user_id', $userId)
            ->whereNotNull('due_date')
            ->whereNotNull('completed_at')
            ->where('due_date', '>=', $thirtyDaysAgo)
            ->whereRaw('completed_at <= due_date')
            ->count();

        $percentage = $totalTasksWithDueDate > 0
            ? round(($completedOnTime / $totalTasksWithDueDate) * 100)
            : 0;

        // Determinar el estado del semáforo
        if ($percentage >= 80) {
            $message = 'Excelente rendimiento';
            $color = 'success';
            $icon = 'heroicon-o-check-circle';
            $statusIcon = '🟢';
        } elseif ($percentage >= 50) {
            $message = 'Rendimiento aceptable';
            $color = 'warning';
            $icon = 'heroicon-o-exclamation-triangle';
            $statusIcon = '🟡';
        } else {
            $message = 'Requiere atención';
            $color = 'danger';
            $icon = 'heroicon-o-x-circle';
            $statusIcon = '🔴';
        }

        return [
            // Card principal del indicador
            Stat::make('Rendimiento General', $percentage.'%')
                ->description($message)
                ->descriptionIcon($icon)
                ->color($color),

            // Card de tareas a tiempo
            Stat::make('A Tiempo', $completedOnTime)
                ->description('Completadas antes del vencimiento')
                ->descriptionIcon('heroicon-o-clock')
                ->color('success'),

            // Card de total de tareas
            Stat::make('Total Tareas', $totalTasksWithDueDate)
                ->description('Con vencimiento (este mes)')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),

            // Card del semáforo Óptimo
            Stat::make('Óptimo', '≥ 80%')
                ->description('Excelente cumplimiento ')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->descriptionColor('success')
                ->color('success'),

            // Card del semáforo Aceptable
            Stat::make('Aceptable', '50% - 79%')
                ->description('Requiere atención ')
                ->descriptionIcon('heroicon-o-minus-circle')
                ->descriptionColor('warning')
                ->color('warning'),

            // Card del semáforo Bajo
            Stat::make('Bajo', '< 50%')
                ->description('Acción inmediata ')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->descriptionColor('danger')
                ->color('danger')
        ];
    }
}
