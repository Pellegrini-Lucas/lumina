<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $title = 'Editar Evento';

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
        if ($data['is_all_day'] ?? false) {
            $startTime = \Carbon\Carbon::parse($data['start_time']);
            $data['end_time'] = $startTime->copy()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        }

        return $data;
    }
}
