<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProjectsDrillDown extends TableWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    public ?int $projectId = null;

    public ?int $taskId = null;

    public string $level = 'projects'; // projects | tasks | subtasks

    protected function getTableHeading(): ?string
    {
        return match ($this->level) {
            'projects' => 'Drill Down: Proyectos (Nivel 1)',
            'tasks' => 'Drill Down: Tareas del Proyecto (Nivel 2) - '.($this->getProjectName() ?? 'Proyecto'),
            'subtasks' => 'Drill Down: Subtareas de la Tarea (Nivel 3) - '.($this->getTaskName() ?? 'Tarea'),
            default => 'Drill Down',
        };
    }

    public function table(Table $table): Table
    {
        return match ($this->level) {
            'projects' => $this->projectsTable($table),
            'tasks' => $this->tasksTable($table),
            'subtasks' => $this->subtasksTable($table),
            default => $table,
        };
    }

    private function projectsTable(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->where('user_id', Auth::id())
                    ->withCount('tasks')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->description('Haz clic para ver tareas →')
                    ->action(fn (Project $record) => $this->drillDown($record->id)),

                TextColumn::make('tasks_count')
                    ->label('Total Tareas')
                    ->badge()
                    ->color('info'),

                TextColumn::make('completed_tasks_count')
                    ->label('Completadas')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function (Project $record) {
                        return $record->tasks()
                            ->where('status', 'completado')
                            ->count();
                    }),

                TextColumn::make('pending_tasks_count')
                    ->label('Pendientes')
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(function (Project $record) {
                        return $record->tasks()
                            ->where('status', 'pendiente')
                            ->count();
                    }),
            ])
            ->recordAction(null)
            ->recordClasses('cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800')
            ->striped()
            ->contentGrid([
                'md' => 1,
            ])
            ->deferLoading();
    }

    private function tasksTable(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->where('user_id', Auth::id())
                    ->where('project_id', $this->projectId)
                    ->withCount('subtasks')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Tarea')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->description('Haz clic para ver subtareas →')
                    ->action(fn (Task $record) => $this->drillDown($record->id)),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state->value) {
                        'pendiente' => 'warning',
                        'en_progreso' => 'info',
                        'completado' => 'success',
                        'cancelada' => 'danger',
                        'vencida' => 'danger',
                    }),

                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn ($state) => match ($state->value) {
                        'baja' => 'gray',
                        'media' => 'info',
                        'alta' => 'danger',
                        'baja' => 'gray',
                        'media' => 'info',
                        'alta' => 'danger',
                    }),

                TextColumn::make('subtasks_count')
                    ->label('Subtareas')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('completed_subtasks')
                    ->label('Sub. Completadas')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function (Task $record) {
                        return $record->subtasks()
                            ->where('is_completed', true)
                            ->count();
                    }),

                TextColumn::make('due_date')
                    ->label('Vencimiento')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('back_to_projects')
                    ->label('← Volver a Proyectos')
                    ->color('gray')
                    ->action(function () {
                        $this->level = 'projects';
                        $this->projectId = null;
                    }),
            ])
            ->recordAction(null)
            ->recordClasses('cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800')
            ->striped();
    }

    private function subtasksTable(Table $table): Table
    {
        return $table
            ->query(
                Subtask::query()
                    ->whereHas('task', fn (Builder $query) => $query->where('user_id', Auth::id()))
                    ->where('task_id', $this->taskId)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Subtarea')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('is_completed')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Completada' : 'Pendiente')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),

                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('back_to_tasks')
                    ->label('← Volver a Tareas')
                    ->color('gray')
                    ->action(function () {
                        $this->level = 'tasks';
                        $this->taskId = null;
                    }),
                Action::make('back_to_projects')
                    ->label('⌂ Volver a Proyectos')
                    ->color('gray')
                    ->action(function () {
                        $this->level = 'projects';
                        $this->projectId = null;
                        $this->taskId = null;
                    }),
            ])
            ->striped();
    }

    private function getProgressColor(Project $record): string
    {
        $total = $record->tasks_count;
        if ($total === 0) {
            return 'gray';
        }
        $completed = $record->tasks()->where('status', 'completado')->count();
        $percentage = ($completed / $total) * 100;

        return match (true) {
            $percentage >= 80 => 'success',
            $percentage >= 50 => 'warning',
            default => 'danger',
        };
    }

    private function getProjectName(): ?string
    {
        if (! $this->projectId) {
            return null;
        }

        return Project::find($this->projectId)?->name;
    }

    private function getTaskName(): ?string
    {
        if (! $this->taskId) {
            return null;
        }

        return Task::find($this->taskId)?->title;
    }

    // Método para manejar el clic en los registros
    public function drillDown(int $recordId): void
    {
        if ($this->level === 'projects') {
            $this->projectId = $recordId;
            $this->level = 'tasks';
        } elseif ($this->level === 'tasks') {
            $this->taskId = $recordId;
            $this->level = 'subtasks';
        }
    }
}
