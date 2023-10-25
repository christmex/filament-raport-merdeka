<?php

namespace App\Filament\Resources\ReligionResource\Pages;

use App\Filament\Resources\ReligionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageReligions extends ManageRecords
{
    protected static string $resource = ReligionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
