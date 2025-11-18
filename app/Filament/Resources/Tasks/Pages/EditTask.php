<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Enums\TaskStatus;
use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected static ?string $title = 'Editar Tarea';

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
            ViewAction::make()->label('Ver'),
            DeleteAction::make()->label('Eliminar'),
            ForceDeleteAction::make()->label('Eliminar permanentemente'),
            RestoreAction::make()->label('Restaurar'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validar que no se pueda cambiar a Pendiente o En Progreso si ya venció
        if (
            isset($data['status']) &&
            in_array($data['status'], [TaskStatus::Pendiente->value, TaskStatus::EnProgreso->value]) &&
            isset($data['due_date']) &&
            $data['due_date'] < now()
        ) {
            Notification::make()
                ->danger()
                ->title('Error de validación')
                ->body('No puedes cambiar una tarea vencida a "Pendiente" o "En Progreso". La fecha de vencimiento ya pasó.')
                ->persistent()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
