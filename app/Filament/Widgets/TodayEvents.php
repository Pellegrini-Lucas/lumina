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
    protected static ?int $sort = 5;

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
                    ->where(function ($query) {
                        $query->where('end_time', '>=', now())
                            ->orWhere(function ($q) {
                                $q->where('is_all_day', true)
                                    ->whereDate('start_time', '>=', today());
                            });
                    })
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
                    ->formatStateUsing(function ($record) {
                        // Si el evento dura más de un día, mostrar fecha completa
                        if ($record->start_time->format('Y-m-d') !== $record->end_time->format('Y-m-d')) {
                            return $record->end_time->format('d/m H:i');
                        }
                        // Si es el mismo día, solo mostrar la hora
                        return $record->end_time->format('H:i');
                    })
                    ->sortable(),

                IconColumn::make('is_all_day')
                    ->label('Todo el día')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label('Período')
                    ->options([
                        'today' => 'Hoy',
                        'week' => 'Esta Semana',
                        'month' => 'Este Mes',
                    ])
                    ->query(function ($query, array $data) {
                        $value = $data['value'] ?? null;
                        
                        if (! $value) {
                            return $query;
                        }

                        return match ($value) {
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
                    }),
            ])
            ->recordAction(null)
            ->recordUrl(fn ($record) => route('filament.app.resources.events.edit', $record))
            ->emptyStateHeading('No hay eventos programados')
            ->emptyStateDescription('No tienes eventos próximos.')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
