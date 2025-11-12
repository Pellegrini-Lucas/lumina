<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class ActiveProjects extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'Mis Proyectos Activos';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->where('user_id', Auth::id())
                    ->withCount([
                        'tasks',
                        'tasks as pending_tasks_count' => fn ($query) => $query->where('status', 'pending'),
                        'tasks as in_progress_tasks_count' => fn ($query) => $query->where('status', 'in_progress'),
                        'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'completed'),
                    ])
                    ->orderByDesc('tasks_count')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-folder-open')
                    ->iconColor(fn (Project $record) => $record->color ?? 'primary'),

                TextColumn::make('tasks_count')
                    ->label('Total Tareas')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('pending_tasks_count')
                    ->label('Pendientes')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('in_progress_tasks_count')
                    ->label('En Progreso')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('completed_tasks_count')
                    ->label('Completadas')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('progress_percentage')
                    ->label('Progreso')
                    ->badge()
                    ->formatStateUsing(function (Project $record) {
                        if ($record->tasks_count === 0) {
                            return '0%';
                        }

                        return round(($record->completed_tasks_count / $record->tasks_count) * 100).'%';
                    })
                    ->color(function (Project $record) {
                        if ($record->tasks_count === 0) {
                            return 'gray';
                        }
                        $percentage = ($record->completed_tasks_count / $record->tasks_count) * 100;

                        return match (true) {
                            $percentage >= 80 => 'success',
                            $percentage >= 50 => 'info',
                            $percentage >= 25 => 'warning',
                            default => 'danger',
                        };
                    }),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(fn (Project $record) => route('filament.app.resources.projects.view', $record))
            ->striped()
            ->defaultSort('tasks_count', 'desc');
    }
}
