<?php

namespace App\Filament\Resources\RaportConfigResource\Pages;

use App\Filament\Resources\RaportConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRaportConfigs extends ManageRecords
{
    protected static string $resource = RaportConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
