<?php

namespace App\Filament\Resources\HomeroomTeacherResource\Pages;

use App\Filament\Resources\HomeroomTeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomeroomTeacher extends EditRecord
{
    protected static string $resource = HomeroomTeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
