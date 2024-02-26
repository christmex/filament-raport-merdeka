<?php

namespace App\Helpers;

use App\Models\Student;
use App\Models\Assessment;
use App\Models\TopicSetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\AssessmentMethodSetting;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class GenerateProgressReport {
    // public static function make(Student $student, $data, bool $isDetailed=false){
    public static function make(Student $student, $data, bool $isDetailed=false){
        // Check if the user is autheticated
        if(auth()->guest()){
            abort(404,'Login First');
        }

        // $isDetailed = true;
        // $data['school_year_id'] = request('school_year_id');
        // $data['school_term_id'] = request('school_term_id');
        // // dd($student,$data);

        $schoolYearId = $data['school_year_id'];
        $schoolTermId = $data['school_term_id'];

        // Get topic setting
        $topicSettings = TopicSetting::orderBy('sort_order')->take(3)->get();

        // Get assessment method setting
        $assessmentMethodSetting = AssessmentMethodSetting::orderBy('order')->get();


        $assessments = Assessment::query()
        ->with('assessmentMethodSetting', 'topicSetting', 'student', 'subjectUserThrough','subjectUser','subjectUser.subject.subjectGroup')
        ->join('subject_users', 'assessments.subject_user_id', '=', 'subject_users.id')
    
        ->join('subjects', 'subject_users.subject_id', '=', 'subjects.id') // Inner join another_table inside subject_users
        ->join('subject_groups', 'subjects.subject_group_id', '=', 'subject_groups.id') // Inner join another_table inside subject_users
        
        ->join('topic_settings', 'assessments.topic_setting_id', '=', 'topic_settings.id')
        ->join('assessment_method_settings', 'assessments.assessment_method_setting_id', '=', 'assessment_method_settings.id')
        
        ->select(
            'subjects.is_curiculum_basic',
            'subject_user_id',
            'assessment_method_setting_id',
            'topic_setting_id',
            'subject_groups.name',
            DB::raw('AVG(grading) as max_grading')
        )
        ->whereIn('topic_setting_id', $topicSettings->pluck('id')->toArray())
        ->where('student_id', $student->id)
        ->where('subject_users.school_year_id', $schoolYearId)
        ->where('subject_users.school_term_id', $schoolTermId)
        ->whereNotNull('grading')
        ->groupBy( 'subject_groups.name','subjects.is_curiculum_basic','assessment_method_setting_id', 'subject_user_id', 'topic_setting_id')
        ->orderBy('subjects.sort_order', 'asc') // Order by the sort_order column from subject_users table
        ->orderBy('subjects.subject_name', 'asc') // Order by the sort_order column from subject_users table
        ->orderBy('subject_groups.order', 'asc') // Order by the sort_order column from subject_users table
        ->orderBy('topic_settings.topic_setting_name','asc')
        ->orderBy('assessment_method_settings.order','asc')
        ->orderByDesc('max_grading') // Order by the maximum grading
        ->withoutGlobalScope('subjectUser')
        ->get();


        // Group By kurikulum
        $dataPublic = [];
        foreach ($assessments as $key => $value) {
            if(!$value->is_curiculum_basic){
                $dataPublic[$value->subjectUserThrough->subject_name][$value->topicSetting->topic_setting_name][$value->assessmentMethodSetting->assessment_method_setting_name] = ['grading' => $value->max_grading];
            }
        }

        $dataBasicCur = [];
        foreach ($assessments as $key => $value) {
            if($value->is_curiculum_basic){
                $dataBasicCur[$value->subjectUserThrough->subject_name][$value->topicSetting->topic_setting_name][$value->assessmentMethodSetting->assessment_method_setting_name] = ['grading' => $value->max_grading];
            }
        }
        
        $thead = $isDetailed ? self::detailedThead($topicSettings, $assessmentMethodSetting) : self::generateThead($topicSettings);

        $view = 'print-progress-report';
        if ($isDetailed) $view ='print-progress-report-detailed';
        $pdf = Pdf::loadView($view, compact('thead','student','dataPublic','dataBasicCur','topicSettings','assessmentMethodSetting'))->setPaper(array(0,0,609.449,935.433), 'portrait');//convert mm to point
        $filaneme = 'progress-report/print-progress-report_'.$student->student_name.'.pdf';
        // return $pdf->stream($filaneme,'public');
        $pdf->save($filaneme,'public');
        Notification::make()
            ->title('Saved successfully')
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->actions([
                Action::make('view')
                    ->url(asset('storage/'.$filaneme))
                    ->link()
                    ->openUrlInNewTab()
                    ,
            ])
            ->send();
    }

    public static function detailedThead($topicSettings, $assessmentMethodSetting){
        $thead = '
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle;width: 5%">No</th>
                <th rowspan="2" style="vertical-align: middle;width: 43%"">Muatan Pelajaran</th>
        ';
        foreach ($topicSettings as $value) {
            $thead .= '<td colspan="5">'.$value->topic_setting_name.'</td>';
        }
        $thead .= '</tr>';

        $th = '';
        for ($i=0; $i < 3; $i++) { 
            foreach ($assessmentMethodSetting as $value) {
                $th .= '<th style="position:relative;height: 120px;">
                    <div class=""  style="transform: rotate(-90deg);white-space: nowrap;display: inline-block;">'.explode('(',$value->assessment_method_setting_name)[0].'</div>
                </th>';
            }
            $th .='<th style="position:relative;height: 120px;">
            <div class=""  style="transform: rotate(-90deg);white-space: nowrap;display: inline-block;">Rata-rata</th>';
        }

        $thead .= '<tr>'.$th.'</tr></thead>';

        return $thead;
    }
    public static function generateThead($topicSettings){
        $thead = '
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle;width: 5%">No</th>
                <th rowspan="2" style="vertical-align: middle;width: 50%"">Muatan Pelajaran</th>
        ';
        foreach ($topicSettings as $value) {
            $thead .= '<td>'.$value->topic_setting_name.'</td>';
        }
        $thead .= '</tr>';


        $thead .= '
                <tr>
                    <th>Rata-rata</th>
                    <th>Rata-rata</th>
                    <th>Rata-rata</th>
                </tr></thead>
                ';

        return $thead;
    }
}