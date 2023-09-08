<?php

namespace App\Filament\Resources\StudentResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    // protected ?string $subheading = 'Caleb\'s homeroom teacher';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // public function getHeader(): ?View
    // {
    //     return view('filament.settings.custom-header');
    // }

    public function getSubheading(): ?string
    {
        $classroom = auth()->user()->activeHomeroom->first()->classroom->classroom_name;
        return "{$classroom}\'s homeroom teacher | School year 2023/2024 ~ Term Genap";
        // return __('Custom Page Subheading');
    }

}
