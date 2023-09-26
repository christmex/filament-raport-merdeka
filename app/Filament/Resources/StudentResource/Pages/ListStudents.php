<?php

namespace App\Filament\Resources\StudentResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use App\Imports\StudentImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
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
            Actions\ActionGroup::make([
                // Actions\Action::make('download_template')
                // ->url(asset('storage/student.xlsx'))->color('info'),

                Actions\Action::make('importStudent')->color('success')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('import_student')
                        ->storeFiles(false)
                        // ->helperText(new HtmlString('Download the excel template \'<strong><a href="'..'">here</a><strong>'))
                        ->helperText(new HtmlString('Please download the excel file to use our format before you upload the file, or ask your admin level'))
                        ->columnSpanFull(),
                ])
                ->action(function(array $data){
                    DB::beginTransaction();
                    try {
                        Excel::import(new StudentImport, $data['import_student']);
                        DB::commit();
                        Notification::make()
                            ->success()
                            ->title('Student imported')
                            ->send();
                    } catch (\Throwable $th) {
                        DB::rollback();
                        Notification::make()
                            ->danger()
                            ->title($th->getMessage())
                            ->send();
                    }
                })
            ])
            ->label('Import')
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('success')
            ->button(),            
        ];
    }

    // public function getHeader(): ?View
    // {
    //     return view('filament.settings.custom-header');
    // }

    public function getSubheading(): ?string
    {
        $classroom = null;
        if(auth()->user()->activeHomeroom->count()){
            $classroom = auth()->user()->activeHomeroom->first()->classroom->classroom_name."'s main teacher | School year ".auth()->user()->activeHomeroom->first()->schoolYear->school_year_name." ~ Term ".auth()->user()->activeHomeroom->first()->schoolTerm->school_term_name;
        }
        return $classroom;
        // return __('Custom Page Subheading');
    }

}
