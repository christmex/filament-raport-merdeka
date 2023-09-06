<?php

namespace App\Filament\Resources\SubjectUserResource\Pages;

use App\Filament\Resources\SubjectUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjectUsers extends ListRecords
{
    protected static string $resource = SubjectUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
