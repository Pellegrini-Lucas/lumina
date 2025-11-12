<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(Auth::id()),

                Section::make('Información de la Tarea')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(4)
                            ->columnSpanFull(),

                        Select::make('project_id')
                            ->label('Proyecto')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Hidden::make('user_id')
                                    ->default(Auth::id()),
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columns(2),

                Section::make('Estado y Prioridad')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options(TaskStatus::class)
                            ->default('pendiente')
                            ->required()
                            ->native(false),

                        Select::make('priority')
                            ->label('Prioridad')
                            ->options(TaskPriority::class)
                            ->default('media')
                            ->required()
                            ->native(false),

                        DateTimePicker::make('due_date')
                            ->label('Fecha de Vencimiento')
                            ->native(false),

                        DateTimePicker::make('completed_at')
                            ->label('Completada el')
                            ->native(false)
                            ->disabled(fn ($get) => $get('status') !== 'completed'),
                    ])
                    ->columns(2),
            ]);
    }
}
