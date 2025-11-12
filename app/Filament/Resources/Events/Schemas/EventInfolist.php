<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Usuario'),
                TextEntry::make('title')
                    ->label('Título'),
                TextEntry::make('start_time')
                    ->label('Hora de Inicio')
                    ->dateTime(),
                TextEntry::make('end_time')
                    ->label('Hora de Fin')
                    ->dateTime(),
                IconEntry::make('is_all_day')
                    ->label('Evento de Todo el Día')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
