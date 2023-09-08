<?php

namespace App\Filament\Resources\HomeroomTeacherResource\Pages;

use App\Filament\Resources\HomeroomTeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeroomTeacher extends CreateRecord
{
    protected static string $resource = HomeroomTeacherResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
