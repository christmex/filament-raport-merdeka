<?php

namespace App\Filament\Resources\AssessmentMethodSettingResource\Pages;

use App\Filament\Resources\AssessmentMethodSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAssessmentMethodSettings extends ManageRecords
{
    protected static string $resource = AssessmentMethodSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
