<?php

namespace App\Filament\Resources\SubjectUserResource\Pages;

use App\Filament\Resources\SubjectUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubjectUser extends EditRecord
{
    protected static string $resource = SubjectUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
