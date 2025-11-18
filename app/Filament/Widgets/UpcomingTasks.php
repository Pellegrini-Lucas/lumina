<?php

namespace App\Filament\Widgets;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class UpcomingTasks extends TableWidget
{
    protected static ?int $sort = 3;

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
                    ->whereIn('status', [TaskStatus::Pendiente, TaskStatus::EnProgreso, TaskStatus::Vencida])
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

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (TaskStatus $state) => match ($state) {
                        TaskStatus::Pendiente => 'warning',
                        TaskStatus::EnProgreso => 'info',
                        TaskStatus::Completado => 'success',
                        TaskStatus::Cancelada => 'danger',
                        TaskStatus::Vencida => 'danger',
                    }),

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
                    ->color(fn ($record) => match (true) {
                        // Si está completada, verificar si se completó antes o después del vencimiento
                        $record->status === TaskStatus::Completado && $record->completed_at && $record->due_date && $record->completed_at->isAfter($record->due_date) => 'danger',
                        $record->status === TaskStatus::Completado => null,
                        // Si está vencida (pendiente o en progreso y pasó la fecha)
                        $record->status === TaskStatus::Vencida => 'danger',
                        $record->due_date->isPast() => 'danger',
                        $record->due_date->isToday() => 'warning',
                        default => null,
                    }),

                TextColumn::make('subtasks_count')
                    ->label('Subtareas')
                    ->counts('subtasks')
                    ->badge()
                    ->color('gray'),
            ])
            ->actions([
                Action::make('complete')
                    ->label('Completar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Marcar tarea como completada')
                    ->modalDescription(fn ($record) => "¿Estás seguro de que quieres marcar '{$record->title}' como completada?")
                    ->modalSubmitActionLabel('Sí, completar')
                    ->action(function (Task $record) {
                        $record->status = TaskStatus::Completado;
                        $record->completed_at = now();
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('Tarea completada')
                            ->body("La tarea '{$record->title}' ha sido marcada como completada.")
                            ->send();
                    })
                    ->visible(fn ($record) => in_array($record->status, [TaskStatus::Pendiente, TaskStatus::EnProgreso, TaskStatus::Vencida])),
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
