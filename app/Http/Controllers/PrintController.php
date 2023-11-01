<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\StudentSemesterEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintController extends Controller
{
    public function print_raport(Student $student){
        if(auth()->guest()){
            abort(404,'Login First');
        }

        $data = [];
        $assessments = Assessment::query()
        ->with('assessmentMethodSetting', 'topicSetting', 'student', 'subjectUserThrough','subjectUser')
        ->join('subject_users', 'assessments.subject_user_id', '=', 'subject_users.id')
    
        ->join('subjects', 'subject_users.subject_id', '=', 'subjects.id') // Inner join another_table inside subject_users
    
        ->select(
            'subject_user_id',
            'assessment_method_setting_id',
            'topic_setting_id',
            DB::raw('AVG(grading) as max_grading')
        )
        ->where('student_id', $student->id)
        ->where('subject_users.school_year_id', SchoolYear::active())
        ->where('subject_users.school_term_id', SchoolTerm::active())
        ->whereNotNull('grading')
        ->groupBy( 'assessment_method_setting_id', 'subject_user_id', 'topic_setting_id')
        ->orderBy('subjects.sort_order', 'asc') // Order by the sort_order column from subject_users table
        ->orderByDesc('max_grading') // Order by the maximum grading
        ->withoutGlobalScope('subjectUser')
        ->get();

        // Group By Subject
        $activeData = [];
        foreach ($assessments as $key => $value) {
            $data[$value->subjectUserThrough->subject_name][$value->topicSetting->topic_setting_name][$value->assessmentMethodSetting->assessment_method_setting_name] = ['grading' => $value->max_grading];
            $data[$value->subjectUserThrough->subject_name]['KKM'] = $value->subjectUser->grade_minimum;
        }

        // Count avg based on the $data
        $newData = Helper::calculateAverage($data);

        // Set the KKM
        foreach ($newData as $key => $value) {
            $newData[$key]['KKM'] = $data[$key]['KKM'];
        }

        // GET THE PAS 
        $StudentSemesterEvaluation = StudentSemesterEvaluation::with('subjectUserThrough')->where('student_id',$student->id)->whereIn('subject_user_id',array_unique($assessments->pluck('subject_user_id')->toArray()))->get();
        

        $finalData = [];

        

        return view('print-raport',compact('student','newData'));


        
    }

    
}
