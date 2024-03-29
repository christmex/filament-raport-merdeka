<?php

namespace App\Filament\Resources\StudentResource\Pages;

use Filament\Actions;
use App\Helpers\Helper;
use App\Helpers\Report;
use App\Models\Student;
use Filament\Forms\Get;
use App\Models\Classroom;
use App\Models\Assessment;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Filament\Tables\Table;
use App\Models\SchoolSetting;
use App\Imports\StudentImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StudentClassroom;
use App\Exports\ReportSheetExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\View;
use App\Helpers\GenerateReportSheet;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Imports\StudentImportAfterExport;
use App\Models\StudentSemesterEvaluation;
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

                Actions\Action::make('importStudentAfterExport')->color('success')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('importStudentAfterExport')
                            ->storeFiles(false)
                            ->helperText(new HtmlString('Before you import, please export the excel first'))
                            ->columnSpanFull(),
                    ])
                    ->hidden(true)
                    ->action(function(array $data){
                        DB::beginTransaction();
                        try {
                            Excel::import(new StudentImportAfterExport, $data['importStudentAfterExport']);
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
                    }),
                Actions\Action::make('importStudent')->color('success')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('import_student')
                            ->storeFiles(false)
                            // ->helperText(new HtmlString('Download the excel template \'<strong><a href="'..'">here</a><strong>'))
                            ->helperText(new HtmlString('Please download the excel file to use our format before you upload the file, or ask your admin level'))
                            ->columnSpanFull(),
                    ])
                    ->hidden(true)
                    ->action(function(array $data){
                            Notification::make()
                                ->warning()
                                ->title('Not available at the moment')
                                ->send();
                        // DB::beginTransaction();
                        // try {
                        //     Excel::import(new StudentImport, $data['import_student']);
                        //     DB::commit();
                        //     Notification::make()
                        //         ->success()
                        //         ->title('Student imported')
                        //         ->send();
                        // } catch (\Throwable $th) {
                        //     DB::rollback();
                        //     Notification::make()
                        //         ->danger()
                        //         ->title($th->getMessage())
                        //         ->send();
                        // }
                    })
            ])
            ->label('Import')
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('success')
            ->button(), 
            
            Actions\Action::make('reportSheet')
                // ->url(route('students.print-report-sheet'))
                // ->openUrlInNewTab()
                ->form([
                    // Select::make('school_year_id')
                    //     ->default(SchoolYear::activeId())
                    //     ->label('School Year')
                    //     ->options(fn()=>SchoolYear::all()->pluck('school_year_name','id'))
                    //     ->required()
                    //     ->live(),
                    // Select::make('school_term_id')
                    //     ->default(SchoolTerm::activeId())
                    //     ->label('School Term')
                    //     ->options(fn()=>SchoolTerm::all()->pluck('school_term_name','id'))
                    //     ->required()
                    //     ->live(),
                    // Select::make('classroom_id')
                    //     ->label('classroom')
                    //     ->options(function(Get $get){
                    //         // return SubjectUser::query()
                    //         //     ->where('user_id',auth()->user()->id)
                    //         //     ->where('school_year_id',$get('school_year_id'))
                    //         //     ->where('school_term_id',$get('school_term_id'))
                    //         //     ->get()
                    //         //     ->pluck('subject_user_name', 'id');
                    //         return Classroom::query()
                    //             ->whereIn(
                    //                 'id',
                    //                 auth()->user()->homerooms
                    //                     ->where('school_year_id',$get('school_year_id'))
                    //                     ->where('school_term_id',$get('school_term_id'))
                    //                     ->pluck('classroom_id')
                    //                     ->toArray())
                    //             ->get()
                    //             ->pluck('classroom_name','id');
                            
                    //         // ->whereIn('id',auth()->user()->activeSubjects->pluck('id')       ->toArray())->get()->pluck('subject_user_name', 'id');
                    //     })

                    //     // ->options(function(){
                    //     //     return Classroom::whereIn('id',auth()->user()->activeHomeroom->first()->classroom_id)->pluck('classroom_name','id');
                    //     // })
                    //     ->required(),
                    Select::make('homeroom_id')
                        ->label('Classroom')
                        ->options(fn()=>auth()->user()->homerooms->pluck('classroom_name','id'))
                        ->required(),
                ])
                ->action(function(array $data){
                    return GenerateReportSheet::make($data['homeroom_id']);
                    // return redirect()->route('print-report-sheet-for-teacher',$data['classroom_id']);
                    // return redirect()->route('print-report-sheet-for-teacher',auth()->user()->activeHomeroom->first()->classroom_id);



                    // return redirect()->route('students.print-report-sheet');
                    // return Excel::download(new ReportSheetExport(Report::generateReportSheet(auth()->user()->activeHomeroom->first()->classroom_id)), 'report_sheet.xlsx');
                })
                ->color('info')
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
