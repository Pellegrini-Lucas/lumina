<?php

namespace App\Filament\Resources\Habits\Schemas;

use App\Enums\HabitFrequency;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class HabitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(Auth::id()),

                Section::make('Información del Hábito')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Hábito')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Hacer ejercicio, Leer, Meditar')
                            ->columnSpanFull(),

                        Select::make('frequency')
                            ->label('Frecuencia')
                            ->options([
                                'daily' => 'Diario',
                                'weekly' => 'Semanal',
                                'monthly' => 'Mensual',
                            ])
                            ->required()
                            ->native(false)
                            ->placeholder('Selecciona una opción')
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => [
                                $set('daily_interval', null),
                                $set('weekly_days', null),
                                $set('weekly_interval', null),
                                $set('monthly_days', null),
                                $set('monthly_interval', null),
                            ])
                            ->columnSpanFull(),
                    ]),

                // Configuración Diaria
Section::make('Configuración Diaria')
                    ->schema([
                        TextInput::make('daily_interval')
                            ->label('Cada cuántos días')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(99)
                            ->default(1)
                            ->step(1)
                            ->suffix('día(s)')
                            ->helperText('El hábito se refrescará cada X días (1-99)'),
                    ])
                    ->visible(fn (Get $get) => $get('frequency') === 'daily'),

                // Configuración Semanal
                Section::make('Configuración Semanal')
                    ->schema([
                        Select::make('weekly_days')
                            ->label('Días de la semana')
                            ->multiple()
                            ->required()
                            ->options([
                                1 => 'Lunes',
                                2 => 'Martes',
                                3 => 'Miércoles',
                                4 => 'Jueves',
                                5 => 'Viernes',
                                6 => 'Sábado',
                                0 => 'Domingo',
                            ])
                            ->native(false)
                            ->placeholder('Selecciona uno o más días')
                            ->helperText('Selecciona los días en que quieres realizar este hábito'),

                        TextInput::make('weekly_interval')
                            ->label('Cada cuántas semanas')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(99)
                            ->default(1)
                            ->step(1)
                            ->suffix('semana(s)')
                            ->helperText('El hábito se refrescará cada X semanas (1-99)'),
                    ])
                    ->columns(2)
                    ->visible(fn (Get $get) => $get('frequency') === 'weekly'),

                // Configuración Mensual
                Section::make('Configuración Mensual')
                    ->schema([
                        Select::make('monthly_days')
                            ->label('Días del mes')
                            ->multiple()
                            ->required()
                            ->options(array_combine(range(1, 31), range(1, 31)))
                            ->native(false)
                            ->placeholder('Selecciona uno o más días')
                            ->helperText('Selecciona los días del mes (1-31) en que quieres realizar este hábito'),

                        TextInput::make('monthly_interval')
                            ->label('Cada cuántos meses')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(12)
                            ->default(1)
                            ->step(1)
                            ->suffix('mes(es)')
                            ->helperText('El hábito se refrescará cada X meses (1-12)'),
                    ])
                    ->columns(2)
                    ->visible(fn (Get $get) => $get('frequency') === 'monthly'),
            ]);
    }
}
