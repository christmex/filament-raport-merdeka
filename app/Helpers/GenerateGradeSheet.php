<?php

namespace App\Helpers;

use App\Models\Assessment;
use App\Models\SubjectUser;
use App\Models\SchoolSetting;
use App\Exports\GradeSheetExport;
use App\Models\SubjectDescription;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AssessmentMethodSetting;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\StudentSemesterEvaluation;

class GenerateGradeSheet {

    public static function make($subjectUserId){
        // Check if the user is autheticated
        if(auth()->guest()){
            abort(404,'Login First');
        }

        $subjectUser = SubjectUser::find($subjectUserId);

        $assessments = Assessment::query()
            ->join('students', 'assessments.student_id', '=', 'students.id')
            ->join('topic_settings', 'assessments.topic_setting_id', '=', 'topic_settings.id')
            ->join('assessment_method_settings', 'assessments.assessment_method_setting_id', '=', 'assessment_method_settings.id')
            ->select(
                'student_id',
                'student_name',
                'student_nis',
                'topic_setting_name',
                'topic_setting_id',
                'assessment_method_setting_name',
                'grading',
            )
            ->whereNotNull('grading')
            ->where('subject_user_id', $subjectUser->id)
            ->orderBy('students.student_name','asc')
            ->orderBy('topic_settings.topic_setting_name','asc')
            ->orderBy('assessment_method_settings.order','asc')
            ->withoutGlobalScope('subjectUser')
            ->get();


        // Group data based on student name
        $data = [];
        foreach ($assessments as $key => $value) {
            $data[$value->student_name]['NIS'] = $value->student_nis;
            $data[$value->student_name][$value->topic_setting_name][$value->assessment_method_setting_name][] = $value->grading;
        }

        
        $totalTopic = [];
        $additionalAssessmentMethodSetting = ['Rata-rata Sumatif','Observasi'];
        $assessmentMethodSetting = AssessmentMethodSetting::orderBy('order')->get();
        
        foreach ($data as $key => $value) {
            foreach ($value as $topicNameKey => $topicNameValue) {
                if($topicNameKey != 'NIS'){
                    $totalTopic[$topicNameKey] = $assessments->where('topic_setting_name',$topicNameKey)->first()->topic_setting_id;
                }
            }
        }
        $thead = '
            <thead>
                <tr>
                    <th rowspan="3">NO</th>
                    <th rowspan="3">Nama</th>
                    <th rowspan="3">NIS</th>
                    <th colspan="'.count($totalTopic) * ($assessmentMethodSetting->count() + count($additionalAssessmentMethodSetting)).'">Penilaian harian Sumatif dan Formatif - '.$subjectUser->subject->subject_name.' kelas '.$subjectUser->classroom->school_level.' '.$subjectUser->classroom->classroom_name.'</th>
                    <th rowspan="3">Rata-rata Sumatif</th>
                    <th rowspan="3">PAS</th>
                    <th rowspan="3">Rata-rata Sumatif + PAS</th>
                    <th rowspan="3" style="width: 500px">Deskripsi</th>
                </tr>
                <tr>
                
        ';

        
        foreach ($totalTopic as $key => $value) {
            $thead .= '<th colspan="'.$assessmentMethodSetting->count() + count($additionalAssessmentMethodSetting).'">'.$key.'</th>';
        }
        $thead .= '</tr><tr>';

        foreach ($totalTopic as $loop) {
            foreach ($assessmentMethodSetting as $key => $value) {
                $thead .= '<th>'.$value->assessment_method_setting_name.'</th>';
            }
            foreach ($additionalAssessmentMethodSetting as $key => $value) {
                $thead .= '<th>'.$value.'</th>';
            }
        }

        $thead .= '</tr>';
        $thead .= '</thead>';

        $studentIds = array_unique($assessments->pluck('student_id')->toArray());
        
        // GET THE PAS 
        $StudentSemesterEvaluation = StudentSemesterEvaluation::with('student')
            ->whereIn('student_id',$studentIds)
            ->where('subject_user_id',$subjectUser->id)
            ->withoutGlobalScope('subjectUser')
            ->whereNotNull('grading')
            ->get();

        $dataPAS = [];
        foreach ($StudentSemesterEvaluation as $key => $value) {
            $dataPAS[$value->student->student_name] = $value->grading;
        }

        $subjectDescription = SubjectDescription::where('subject_user_id',$subjectUser->id)->get();
        $grade_minimum = $subjectUser->grade_minimum;

        $getSchoolSettings = SchoolSetting::first();
        $avgDiv = ($getSchoolSettings->sumatif_avg/100);
        $PASDiv = ($getSchoolSettings->pas_avg/100);

        // dd($data['Tyndale James Shumaker']);
        $filename = 'grade_sheet - '.$subjectUser->subject->subject_name.' kelas '.$subjectUser->classroom->school_level.' '.$subjectUser->classroom->classroom_name.'.xlsx';
        Notification::make()
            ->title('Saved successfully')
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->send();
        return Excel::download(new GradeSheetExport(compact('data','thead','assessmentMethodSetting','totalTopic','dataPAS','subjectDescription','grade_minimum','avgDiv','PASDiv')), $filename);

    }
}