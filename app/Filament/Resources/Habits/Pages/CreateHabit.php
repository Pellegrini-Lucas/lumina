<?php

namespace App\Filament\Resources\Habits\Pages;

use App\Filament\Resources\Habits\HabitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHabit extends CreateRecord
{
    protected static string $resource = HabitResource::class;

    protected static ?string $title = 'Crear Hábito';

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
}
