<?php

namespace App\Filament\Resources\SubjectGroupResource\Pages;

use App\Filament\Resources\SubjectGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSubjectGroups extends ManageRecords
{
    protected static string $resource = SubjectGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
