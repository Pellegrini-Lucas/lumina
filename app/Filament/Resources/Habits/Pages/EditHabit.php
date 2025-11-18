<?php

namespace App\Filament\Resources\Habits\Pages;

use App\Filament\Resources\Habits\HabitResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHabit extends EditRecord
{
    protected static string $resource = HabitResource::class;

    protected static ?string $title = 'Editar Hábito';

    protected static ?string $breadcrumb = 'Editar';

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()->label('Guardar cambios');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Eliminar'),
            ForceDeleteAction::make()->label('Eliminar permanentemente'),
            RestoreAction::make()->label('Restaurar'),
        ];
    }
}
