<?php

namespace App\Filament\Resources\SubjectUserResource\Pages;

use Filament\Actions;
use App\Helpers\Helper;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SubjectUserResource;

class ListSubjectUsers extends ListRecords
{
    protected static string $resource = SubjectUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getSubheading(): ?string
    {
        $classroom = null;
        if(auth()->user()){
            $classroom = 'Current School Year: '.Helper::getSchoolYearName().' - '.Helper::getSchoolTermName();
        }
        return $classroom;
    }
}
