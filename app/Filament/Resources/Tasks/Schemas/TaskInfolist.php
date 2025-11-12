<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Usuario'),
                TextEntry::make('project.name')
                    ->label('Proyecto')
                    ->placeholder('-'),
                TextEntry::make('title')
                    ->label('Título'),
                TextEntry::make('description')
                    ->label('Descripción')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge()
                    ->label('Estado'),
                TextEntry::make('priority')
                    ->badge()
                    ->label('Prioridad'),
                TextEntry::make('due_date')
                    ->dateTime()
                    ->placeholder('-')
                    ->label('Fecha de Vencimiento'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-')
                    ->label('Fecha de Finalización'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-')
                    ->label('Fecha de Creación'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-')
                    ->label('Fecha de Actualización'),
            ]);
    }
}
