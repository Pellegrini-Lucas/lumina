<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $title = 'Crear Evento';

    protected static ?string $breadcrumb = 'Crear';

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()->label('Crear');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()->label('Crear y crear otro');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['is_all_day'] ?? false) {
            $startTime = \Carbon\Carbon::parse($data['start_time']);
            $data['end_time'] = $startTime->copy()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        }

        return $data;
    }
}
