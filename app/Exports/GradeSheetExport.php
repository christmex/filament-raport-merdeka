<?php

namespace App\Exports;

use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Assessment;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\SchoolSetting;
use App\Models\CharacterReport;
use App\Models\StudentClassroom;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\StudentSemesterEvaluation;

class GradeSheetExport implements FromView
{
    public $data;
    public function __construct($data){
        $this->data = $data;
    }
    public function view(): View
    {
        return view('exports.grade-sheet', $this->data);
    }

}
