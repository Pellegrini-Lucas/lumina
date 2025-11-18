<?php

namespace App\Filament\Widgets;

use App\Models\Habit;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class TodayHabitsWidget extends TableWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'Mis Hábitos';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Habit::query()
                    ->where('user_id', Auth::id())
                    ->orderBy('name')
            )
            ->columns([
                TextColumn::make('id')
                    ->label('')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $color = $record->getStreakColor();
                        
                        return "<div style='width: 24px; height: 24px; border-radius: 9999px; background-color: {$color}; transition: all 0.3s;'></div>";
                    })
                    ->width('50px')
                    ->alignCenter()
                    ->sortable(false)
                    ->searchable(false),

                TextColumn::make('name')
                    ->label('Hábito')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->frequency->getLabel()),

                TextColumn::make('streak')
                    ->label('Racha Actual')
                    ->state(fn ($record) => $record->getCurrentStreak())
                    ->formatStateUsing(fn ($state) => $state . ' día' . ($state !== 1 ? 's' : ''))
                    ->sortable(false)
                    ->alignCenter()
                    ->width('120px'),

                TextColumn::make('best_streak')
                    ->label('Mejor Racha')
                    ->formatStateUsing(fn ($state) => ($state ?? 0) . ' día' . (($state ?? 0) !== 1 ? 's' : ''))
                    ->sortable()
                    ->alignCenter()
                    ->width('120px'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->state(fn ($record) => $record->isDueToday() && !$record->isCompletedToday() ? 'Por completar' : ' ')
                    ->formatStateUsing(function ($state) {
                        if ($state === 'Por completar') {
                            return new HtmlString('<span style="color: #eab308; font-weight: 500;">Por completar</span>');
                        }
                        return new HtmlString('<span class="opacity-0">.</span>');
                    })
                    ->sortable(false)
                    ->width('150px'),
            ])
            ->actions([
                Action::make('complete')
                    ->label('')
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->color('success')
                    ->action(function (Habit $record) {
                        $record->completeForToday();

                        Notification::make()
                            ->success()
                            ->title('¡Hábito completado!')
                            ->body("Has completado '{$record->name}' hoy.")
                            ->send();
                    })
                    ->visible(fn ($record) => $record->isDueToday() && !$record->isCompletedToday()),
            ])
            ->emptyStateHeading('No tienes hábitos creados')
            ->emptyStateDescription('Crea tu primer hábito para comenzar.')
            ->emptyStateIcon('heroicon-o-sparkles');
    }
}
