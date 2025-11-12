<?php

namespace App\Filament\Widgets;

use App\Enums\TaskStatus;
use App\Models\Event;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        $today = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        // Tareas pendientes
        $pendingTasks = Task::where('user_id', $userId)
            ->where('status', TaskStatus::Pendiente)
            ->count();

        // Tareas completadas hoy
        $completedToday = Task::where('user_id', $userId)
            ->where('status', TaskStatus::Completado)
            ->whereBetween('completed_at', [$today, $endOfDay])
            ->count();

        // Eventos de hoy
        $eventsToday = Event::where('user_id', $userId)
            ->whereBetween('start_time', [$today, $endOfDay])
            ->count();

        return [
            Stat::make('Tareas Pendientes', $pendingTasks)
                ->description('Total de tareas por completar')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('warning'),

            Stat::make('Completadas Hoy', $completedToday)
                ->description('Tareas finalizadas en el día')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Eventos Hoy', $eventsToday)
                ->description('Eventos programados para hoy')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),
        ];
    }
}
