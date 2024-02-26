<?php

namespace App\Helpers;

use App\Models\Student;
use App\Models\Assessment;
use App\Models\SchoolSetting;
use App\Models\CharacterReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Models\StudentSemesterEvaluation;
use Filament\Notifications\Actions\Action;

class GenerateStudentCharacterReport {

public static function make(Student $student, $form){
        if(auth()->guest()){
            abort(404,'Login First');
        }
        $data = [];
        $characterReports = CharacterReport::query()
            ->with('habit', 'student')
            ->join('habits', 'character_reports.habit_id', '=', 'habits.id')
            ->select(
                'habits.aspect_id',
                'habit_id',
                'week',
                'home',
                'school',
            )
            ->where('student_id', $student->id)
            ->where('school_year_id', $form['school_year_id'])
            ->where('school_term_id', $form['school_term_id'])

            ->groupBy( 'habits.aspect_id','habit_id','week','home','school')

            ->orderBy('habits.id', 'asc') // Order by the sort_order column from subject_users table
            ->orderBy('week','asc') // Order by the maximum grading

            ->get();
        ;

        // Group By Subject
        $activeData = [];
        foreach ($characterReports as $value) {
            $data[$value->habit->aspect->name][$value->habit->name][$value->week] = ['home' => $value->home,'school' => $value->school];
        }
        if(!count($characterReports)){
            Notification::make()
                    ->danger()
                    ->persistent()
                    ->title('go to "Character report" menu to create character raport first then we can generate the report sheet')
                    ->send();
                // If this ishappen stopthe prosses
                return back();
        }

        // ->setPaper(array(0,0,935.433,609.449), 'potrait')
        $avgAcademic = self::generateAcademyAvg([$student->id], $form);
        // dd(Helper::customRound($avgAcademic,10));
        $pdf = Pdf::loadView('print-report-character', compact('data','avgAcademic','student'))->setPaper(array(0,0,935.433,609.449), 'potrait');//convert mm to point
        $filaneme = 'report-character/print-report-character-'.$student->student_name.'.pdf';
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

        // dd($data);
    }

    public static function generateAcademyAvg($studentId, $form){
        $data = [];

        $assessments = Assessment::query()
        ->with('assessmentMethodSetting', 'topicSetting', 'student', 'subjectUserThrough','subjectUser')
        ->join('subject_users', 'assessments.subject_user_id', '=', 'subject_users.id')
    
        ->join('subjects', 'subject_users.subject_id', '=', 'subjects.id') // Inner join another_table inside subject_users
        ->join('students', 'assessments.student_id', '=', 'students.id') // Join the students table
        ->select(
            'subjects.is_curiculum_basic',
            'subject_user_id',
            'assessment_method_setting_id',
            'topic_setting_id',
            'student_id',
            DB::raw('AVG(grading) as max_grading')
        )
        ->whereIn('student_id', $studentId)
        ->where('subject_users.school_year_id', $form['school_year_id'])
        ->where('subject_users.school_term_id', $form['school_term_id'])
        ->whereNotNull('grading')
        ->groupBy( 'subjects.is_curiculum_basic','assessment_method_setting_id', 'subject_user_id', 'topic_setting_id','student_id')
        ->orderBy('subjects.sort_order', 'asc') // Order by the sort_order column from subject_users table
        ->orderBy('subjects.subject_name', 'asc') // Order by the sort_order column from subject_users table
        ->orderBy('students.student_name', 'asc')
        ->orderByDesc('max_grading') // Order by the maximum grading
        ->withoutGlobalScope('subjectUser')
        ->get();


        // Group By Subject
        $activeData = [];
        foreach ($assessments as $key => $value) {
            $data[$value->student->student_name][$value->subjectUserThrough->subject_name][$value->topicSetting->id][$value->assessmentMethodSetting->assessment_method_setting_name] = ['grading' => $value->max_grading];
            $data[$value->student->student_name][$value->subjectUserThrough->subject_name]['KKM'] = $value->subjectUser->grade_minimum;
            $data[$value->student->student_name][$value->subjectUserThrough->subject_name]['is_curiculum_basic'] = $value->is_curiculum_basic;
        }

        // Count avg based on the $data
        $newData = Helper::reportSheetCalculateAverage($data);
        // Sort by student name
        ksort($newData);

        // GET THE PAS 
        $StudentSemesterEvaluation = StudentSemesterEvaluation::with('subjectUserThrough')
        ->whereIn('student_id',$studentId)
        ->whereIn('subject_user_id',array_unique($assessments->pluck('subject_user_id')->toArray()))
        ->withoutGlobalScope('subjectUser')
        ->get();

        // Set the KKM
        foreach ($newData as $newDataKey => $newDataValue) {
            foreach ($newDataValue as $key => $value) {
                $newData[$newDataKey][$key]['KKM'] = $data[$newDataKey][$key]['KKM'];
                $newData[$newDataKey][$key]['PAS'] = null;
                // $newData[$newDataKey][$key]['subject_user_id'] = $data[$newDataKey][$key]['subject_user_id'] ; //this is exist for description in this case we dont need the description
                $newData[$newDataKey][$key]['is_curiculum_basic'] = $data[$newDataKey][$key]['is_curiculum_basic'];

                if($StudentSemesterEvaluation->where('subjectUserThrough.subject_name',$key)->first()){
                    $newData[$newDataKey][$key]['PAS'] =$StudentSemesterEvaluation->where('subjectUserThrough.subject_name',$key)->first()->grading;
                }
            }
        }

        $getSchoolSettings = SchoolSetting::first();
        $avgDiv = ($getSchoolSettings->sumatif_avg/100);
        $PASDiv = ($getSchoolSettings->pas_avg/100);

        // Check apakah semua anak sudah memiliki semua nilai dimapel?,semua anak harus sama jumlah mapelnya dan urutannya harussama,lakukan pengecekan agar tidak salah menepatkannilai di tabel nanti
        $firstCount = 0;
        $firstArrayData = null;
        foreach ($newData as $key => $value) {
            if ($key === array_key_first($newData)) {
                $firstCount = count($value);
                $firstArrayData = $value;
            }
            if(count($value) != $firstCount){
                dd('FAILURE, please think about this');
            }
        }

        $finalNewData = [];
        
        $schoolCurriculum = [];
        foreach ($newData as $studentKey => $studentValue) {
            foreach ($studentValue as $key => $value) { 
                if($newData[$studentKey][$key]['is_curiculum_basic']  == 0){
                    $finalNewData[$studentKey][$key] = $newData[$studentKey][$key];
                }
            }
        }

        $basicCurriculum = [];
        foreach ($newData as $studentKey => $studentValue) {
            foreach ($studentValue as $key => $value) { 
                if($newData[$studentKey][$key]['is_curiculum_basic']){
                    $finalNewData[$studentKey][$key] = $newData[$studentKey][$key];
                }
            }
        }

        $avg = [];
        foreach($finalNewData as $student_name => $subjects ){
            foreach($subjects as $subjectKey => $subjectValue ){
                array_push($avg, Helper::countFinalGrade($subjectValue['AVG'],$subjectValue['PAS'],$avgDiv, $PASDiv));
                Helper::countFinalGrade($subjectValue['AVG'],$subjectValue['PAS'],$avgDiv, $PASDiv);
            }

            ;
        }
        return array_sum($avg) / count($avg);
    }
}