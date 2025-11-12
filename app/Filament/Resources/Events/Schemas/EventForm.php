<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(Auth::id()),

                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                DateTimePicker::make('start_time')
                    ->label('Fecha y Hora de Inicio')
                    ->required()
                    ->native(false)
                    ->seconds(false),

                DateTimePicker::make('end_time')
                    ->label('Fecha y Hora de Fin')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->locale('es')
                    ->after('start_time'),

                Toggle::make('is_all_day')
                    ->label('Evento de Todo el Día')
                    ->default(false)
                    ->columnSpanFull(),
            ]);
    }
}
