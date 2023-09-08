<?php

namespace App\Filament\Resources\HomeroomTeacherResource\Pages;

use App\Filament\Resources\HomeroomTeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeroomTeachers extends ListRecords
{
    protected static string $resource = HomeroomTeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
