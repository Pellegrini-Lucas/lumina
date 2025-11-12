<?php

namespace App\Filament\Widgets;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class UpcomingTasks extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'Mis Tareas Pendientes';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->where('user_id', Auth::id())
                    ->where('status', TaskStatus::Pendiente)
                    ->whereNotNull('due_date')
                    ->orderBy('due_date', 'asc')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Tarea')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->description(fn ($record) => $record->project?->name),

                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (TaskPriority $state) => match ($state) {
                        TaskPriority::Baja => 'gray',
                        TaskPriority::Media => 'info',
                        TaskPriority::Alta => 'danger',
                    }),

                TextColumn::make('due_date')
                    ->label('Vencimiento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn ($state) => $state->diffForHumans())
                    ->color(fn ($state) => match (true) {
                        $state->isPast() => 'danger',
                        $state->isToday() => 'warning',
                        default => null,
                    }),

                TextColumn::make('subtasks_count')
                    ->label('Subtareas')
                    ->counts('subtasks')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options(TaskPriority::class)
                    ->native(false),

                SelectFilter::make('period')
                    ->label('Período')
                    ->options([
                        'today' => 'Hoy',
                        'week' => 'Esta Semana',
                        'month' => 'Este Mes',
                    ])
                    ->query(function ($query, $state) {
                        return match ($state['value'] ?? null) {
                            'today' => $query->whereDate('due_date', today()),
                            'week' => $query->whereBetween('due_date', [
                                now()->startOfWeek(),
                                now()->endOfWeek(),
                            ]),
                            'month' => $query->whereBetween('due_date', [
                                now()->startOfMonth(),
                                now()->endOfMonth(),
                            ]),
                            default => $query,
                        };
                    })
                    ->default('week'),
            ])
            ->recordAction(null)
            ->recordUrl(fn ($record) => route('filament.app.resources.tasks.edit', $record))
            ->emptyStateHeading('¡No hay tareas pendientes!')
            ->emptyStateDescription('Excelente trabajo. No tienes tareas pendientes con fecha de vencimiento.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
