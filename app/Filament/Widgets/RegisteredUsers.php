<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RegisteredUsers extends TableWidget
{
    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'ScoreBoard - Usuarios Registrados';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->withCount([
                        'projects', 
                        'tasks', 
                        'events',
                        'tasks as completed_tasks_count' => fn($query) => $query->where('status', \App\Enums\TaskStatus::Completado)
                    ])
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('projects_count')
                    ->label('Proyectos')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('tasks_count')
                    ->label('Tareas')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('events_count')
                    ->label('Eventos')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('completed_tasks_count')
                    ->label('Tareas Completadas')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn ($state) => $state->diffForHumans()),
            ])
            ->recordAction(null)
            ->emptyStateHeading('No hay usuarios registrados')
            ->emptyStateDescription('No se encontraron usuarios en el sistema.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->striped();
    }
}
