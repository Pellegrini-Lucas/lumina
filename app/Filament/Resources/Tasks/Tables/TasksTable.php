<?php

namespace App\Filament\Resources\Tasks\Tables;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Tarea')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->description)
                    ->limit(50),

                TextColumn::make('project.name')
                    ->label('Proyecto')
                    ->badge()
                    ->color(fn ($record) => $record->project?->color)
                    ->searchable()
                    ->sortable(),

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
                    ->color(fn ($record) => match (true) {
                        // Si está completada, verificar si se completó antes o después del vencimiento
                        $record->status === TaskStatus::Completado && $record->completed_at && $record->due_date && $record->completed_at->isAfter($record->due_date) => 'danger',
                        $record->status === TaskStatus::Completado => null,
                        // Si está vencida (pendiente o en progreso y pasó la fecha)
                        $record->status === TaskStatus::Vencida => 'danger',
                        $record->due_date && $record->due_date->isPast() => 'danger',
                        $record->due_date && $record->due_date->isToday() => 'warning',
                        default => null,
                    }),

                TextColumn::make('completed_at')
                    ->label('Completada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('due_date', 'asc')
            ->searchPlaceholder('Buscar tareas...')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(TaskStatus::class)
                    ->native(false),

                SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options(TaskPriority::class)
                    ->native(false),

                SelectFilter::make('project')
                    ->label('Proyecto')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Editar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
