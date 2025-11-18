<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class EventsProjectionChart extends ChartWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Proyección de Eventos (Próximos 30 días)';

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $userId = Auth::id();
        $startDate = now()->startOfDay();
        $endDate = now()->addDays(30)->endOfDay();

        // Obtener todos los eventos del usuario en los próximos 30 días
        $events = Event::where('user_id', $userId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->orderBy('start_time')
            ->get();

        // Agrupar eventos por día
        $eventsByDay = [];
        for ($i = 0; $i < 30; $i++) {
            $date = now()->addDays($i)->format('Y-m-d');
            $eventsByDay[$date] = 0;
        }

        // Contar eventos por día
        foreach ($events as $event) {
            $date = Carbon::parse($event->start_time)->format('Y-m-d');
            if (isset($eventsByDay[$date])) {
                $eventsByDay[$date]++;
            }
        }

        // Preparar datos para la gráfica
        $labels = [];
        $data = [];

        foreach ($eventsByDay as $date => $count) {
            $labels[] = Carbon::parse($date)->format('d/m');
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Eventos programados',
                    'data' => $data,
                    'backgroundColor' => 'rgba(72, 46, 134, 0.2)',
                    'borderColor' => 'rgb(139, 92, 246)',
                    'borderWidth' => 1,
                    'tension' => 0.2,
                    'fill' => true,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
