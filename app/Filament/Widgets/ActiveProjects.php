<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class ActiveProjects extends TableWidget
{
    protected static ?int $sort = 2;

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
                        'tasks as pending_tasks_count' => fn ($query) => $query->where('status', 'pendiente'),
                        'tasks as in_progress_tasks_count' => fn ($query) => $query->where('status', 'en_progreso'),
                        'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'completado'),
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
