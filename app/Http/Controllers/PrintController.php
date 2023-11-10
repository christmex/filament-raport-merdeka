<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use App\Models\SchoolSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SubjectDescription;
use Illuminate\Support\Facades\DB;
use App\Models\StudentSemesterEvaluation;

class PrintController extends Controller
{
    public function print_raport_cover(Student $student){
        if(auth()->guest()){
            abort(404,'Login First');
        }

        $pdf = Pdf::loadView('print-raport-cover', compact('student'))->setPaper(array(0,0,612.283,935.433), 'portrait');//convert mm to point
        return $pdf->stream('print-raport-cover.pdf');
        // return $pdf->render();

        return view('print-raport-cover',compact('student'));
    }
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
            'subjects.is_curiculum_basic',
            'subject_user_id',
            'assessment_method_setting_id',
            'topic_setting_id',
            DB::raw('AVG(grading) as max_grading')
        )
        ->where('student_id', $student->id)
        ->where('subject_users.school_year_id', SchoolYear::active())
        ->where('subject_users.school_term_id', SchoolTerm::active())
        ->whereNotNull('grading')
        ->groupBy( 'subjects.is_curiculum_basic','assessment_method_setting_id', 'subject_user_id', 'topic_setting_id')
        ->orderBy('subjects.sort_order', 'asc') // Order by the sort_order column from subject_users table
        ->orderByDesc('max_grading') // Order by the maximum grading
        ->withoutGlobalScope('subjectUser')
        ->get();

        // Group By Subject
        $activeData = [];
        foreach ($assessments as $key => $value) {
            $data[$value->subjectUserThrough->subject_name][$value->topicSetting->id][$value->assessmentMethodSetting->assessment_method_setting_name] = ['grading' => $value->max_grading];
            $data[$value->subjectUserThrough->subject_name]['KKM'] = $value->subjectUser->grade_minimum;
            $data[$value->subjectUserThrough->subject_name]['subject_user_id'] = $value->subject_user_id;
            $data[$value->subjectUserThrough->subject_name]['is_curiculum_basic'] = $value->is_curiculum_basic;
        }

        // dd($data);
        // dd($assessments->whereIn('subject_user_id',[78,46])->toArray());
        
        // Count avg based on the $data
        $newData = Helper::calculateAverage($data);
        
        $avgPerTopic = Helper::calculateAvgTopic($data);

        // GET THE PAS 
        $StudentSemesterEvaluation = StudentSemesterEvaluation::with('subjectUserThrough')
        ->where('student_id',$student->id)
        ->whereIn('subject_user_id',array_unique($assessments->pluck('subject_user_id')->toArray()))
        ->withoutGlobalScope('subjectUser')
        ->get();
  
        // Set the KKM
        foreach ($newData as $key => $value) {
            $newData[$key]['KKM'] = $data[$key]['KKM'];
            $newData[$key]['PAS'] = null;
            $newData[$key]['subject_user_id'] = $data[$key]['subject_user_id'] ;
            $newData[$key]['is_curiculum_basic'] = $data[$key]['is_curiculum_basic'] ;

            if($StudentSemesterEvaluation->where('subjectUserThrough.subject_name',$key)->first()){
                $newData[$key]['PAS'] =$StudentSemesterEvaluation->where('subjectUserThrough.subject_name',$key)->first()->grading;
            }
        }

        $minMaxArray = [];


        foreach ($avgPerTopic as $subject => $topics) {
            
            $newData[$subject]['minMax_topic_id'] = [
                Helper::getKeyByValue($topics, min($topics)) => min($topics),
                Helper::getKeyByValue($topics, max($topics)) => max($topics)
            ];
        }


        $getSchoolSettings = SchoolSetting::first();
        $avgDiv = ($getSchoolSettings->sumatif_avg/100);
        $PASDiv = ($getSchoolSettings->pas_avg/100);

        $topicSettingIds = [];
        foreach ($newData as $key => $value) {
            foreach ($value['minMax_topic_id'] as $subKey => $subValue) {
                $topicSettingIds[] = $subKey;
            }
        }
        
        $subjectUserIds = [];
        foreach ($newData as $key => $value) {
            $subjectUserIds[] = $value['subject_user_id'];
        }


        $subjectDescription = SubjectDescription::query()
        ->with('subjectUser')
        ->whereIn('topic_setting_id',array_unique($topicSettingIds))
        ->whereIn('subject_user_id',array_unique($subjectUserIds))
        ->withoutGlobalScope('subjectUser')
        ->get();

        // dd($newData);
        $basicCurriculum = [];
        foreach ($newData as $key => $value) { 
            if($newData[$key]['is_curiculum_basic']){
                $basicCurriculum[$key] = $newData[$key];
            }
        }

        $schoolCurriculum = [];
        foreach ($newData as $key => $value) { 
            if($newData[$key]['is_curiculum_basic'] == 0){
                $schoolCurriculum[$key] = $newData[$key];
            }
        }

        // dd($basicCurriculum,$schoolCurriculum, $newData);
        // return view('print-raport',compact('student','basicCurriculum','schoolCurriculum','avgDiv','PASDiv','subjectDescription'));

    
        // ->where('range_start', '<=', 84)
        // ->where('range_end', '>=', 84)
        // ->first());
        // dd(array_unique($topicSettingIds));

        // $pdf = Pdf::loadView('print-raport-cover', compact('student'))->setPaper(array(0,0,612.283,935.433), 'portrait');//convert mm to point
        // // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,595.28,935.433), 'portrait');//convert mm to point
        // return $pdf->stream('print-raport-cover.pdf');

        
        // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,609.4488,935.433), 'portrait');
        // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,612.283,935.433), 'portrait');//convert mm to point 216 mm
        $pdf = Pdf::loadView('print-raport', compact('student','basicCurriculum','schoolCurriculum','avgDiv','PASDiv','subjectDescription'))->setPaper(array(0,0,609.449,935.433), 'portrait');//convert mm to point
        // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,595.28,935.433), 'portrait');//convert mm to point
        return $pdf->stream('print-raport.pdf');

        


        
    }

    
}
