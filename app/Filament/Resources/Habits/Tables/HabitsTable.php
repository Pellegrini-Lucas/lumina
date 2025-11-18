<?php

namespace App\Filament\Resources\Habits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class HabitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('frequency')
                    ->label('Frecuencia')
                    ->badge()
                    ->searchable(),
                TextColumn::make('daily_interval')
                    ->label('Intervalo diario')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weekly_interval')
                    ->label('Intervalo semanal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('monthly_interval')
                    ->label('Intervalo mensual')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_completed_at')
                    ->label('Última completación')
                    ->date()
                    ->sortable(),
                TextColumn::make('next_due_date')
                    ->label('Próxima fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('streak')
                    ->label('Racha')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('best_streak')
                    ->label('Mejor racha')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_completions')
                    ->label('Total completados')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label('Eliminado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchPlaceholder('Buscar hábitos...')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
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
