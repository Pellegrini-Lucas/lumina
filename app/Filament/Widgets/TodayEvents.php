<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class TodayEvents extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'Mis Eventos Próximos';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::query()
                    ->where('user_id', Auth::id())
                    ->where('start_time', '>=', now())
                    ->orderBy('start_time', 'asc')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Evento')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('start_time')
                    ->label('Inicio')
                    ->dateTime('d/m H:i')
                    ->sortable()
                    ->description(fn ($state) => $state->diffForHumans()),

                TextColumn::make('end_time')
                    ->label('Fin')
                    ->dateTime('H:i')
                    ->sortable(),

                IconColumn::make('is_all_day')
                    ->label('Todo el día')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('duration')
                    ->label('Duración')
                    ->formatStateUsing(function ($record) {
                        if ($record->is_all_day) {
                            return 'Todo el día';
                        }
                        $diff = $record->start_time->diff($record->end_time);

                        return $diff->h > 0
                            ? "{$diff->h}h {$diff->i}m"
                            : "{$diff->i}m";
                    })
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label('Período')
                    ->options([
                        'today' => 'Hoy',
                        'week' => 'Esta Semana',
                        'month' => 'Este Mes',
                    ])
                    ->query(function ($query, $state) {
                        return match ($state['value'] ?? null) {
                            'today' => $query->whereDate('start_time', today()),
                            'week' => $query->whereBetween('start_time', [
                                now()->startOfWeek(),
                                now()->endOfWeek(),
                            ]),
                            'month' => $query->whereBetween('start_time', [
                                now()->startOfMonth(),
                                now()->endOfMonth(),
                            ]),
                            default => $query,
                        };
                    })
                    ->default('week'),
            ])
            ->recordAction(null)
            ->recordUrl(fn ($record) => route('filament.app.resources.events.edit', $record))
            ->emptyStateHeading('No hay eventos programados')
            ->emptyStateDescription('No tienes eventos próximos.')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
