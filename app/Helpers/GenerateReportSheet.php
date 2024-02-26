<?php

namespace App\Helpers;

use App\Models\Student;
use App\Models\Assessment;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\SchoolSetting;
use App\Models\CharacterReport;
use App\Models\HomeroomTeacher;
use App\Models\StudentClassroom;
use App\Exports\ReportSheetExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use App\Models\StudentSemesterEvaluation;

class GenerateReportSheet {


    public static function make($homeroomId){
        DB::beginTransaction();
        try {

            if(auth()->guest()){
                abort(404,'Login First');
            }
            $classroom = HomeroomTeacher::find($homeroomId);
            
            $classroom_id = $classroom->id;
            $classroom_name = $classroom->classroom_name;

            $data = [];

            $StudentClassroom = StudentClassroom::query()
                ->where('school_year_id',$classroom->school_year_id)
                ->where('school_term_id',$classroom->school_term_id)
                ->where('classroom_id',$classroom_id)
                ->get()
                ->pluck('student_id')
                ->toArray();

            $studentIds = Student::whereIn('id',$StudentClassroom)
                            ->get()
                            ->pluck('id')
                            ->toArray();

            if(!count($studentIds)){
                throw new \Exception('There is no assessment in that class yet!');
            }
            $assessments = Assessment::query()
                ->with('assessmentMethodSetting', 'topicSetting', 'student', 'subjectUserThrough','subjectUser')
                ->join('subject_users', 'assessments.subject_user_id', '=', 'subject_users.id')
            
                ->join('subjects', 'subject_users.subject_id', '=', 'subjects.id') // Inner join another_table inside subject_users
                ->join('students', 'assessments.student_id', '=', 'students.id') // Join the students table
                ->join('topic_settings', 'assessments.topic_setting_id', '=', 'topic_settings.id')
                ->join('assessment_method_settings', 'assessments.assessment_method_setting_id', '=', 'assessment_method_settings.id')
                ->select(
                    'subjects.is_curiculum_basic',
                    'subject_user_id',
                    'assessment_method_setting_id',
                    'topic_setting_id',
                    'student_id',
                    DB::raw('AVG(grading) as max_grading')
                    // DB::raw('grading as max_grading')
                )
                ->whereIn('student_id', $studentIds)
                ->where('subject_users.school_year_id', $classroom->school_year_id)
                ->where('subject_users.school_term_id', $classroom->school_term_id)
                ->whereNotNull('grading')
                ->groupBy( 'subjects.is_curiculum_basic','assessment_method_setting_id', 'subject_user_id', 'topic_setting_id','student_id')
                ->orderBy('subjects.sort_order', 'asc') // Order by the sort_order column from subject_users table
                ->orderBy('subjects.subject_name', 'asc') // Order by the sort_order column from subject_users table
                ->orderBy('students.student_name', 'asc')
                ->orderBy('topic_settings.topic_setting_name','asc')
                ->orderBy('assessment_method_settings.order','asc')
                ->orderByDesc('max_grading') // Order by the maximum grading
                ->withoutGlobalScope('subjectUser')
                ->get();

            // Group By Subject
            $activeData = [];
            foreach ($assessments as $key => $value) {
                $data[$value->student->student_name][$value->subjectUserThrough->subject_name][$value->topicSetting->id][$value->assessmentMethodSetting->assessment_method_setting_name] = ['grading' => $value->max_grading];
                // $data[$value->student->student_name][$value->subjectUserThrough->subject_name]['KKM'] = $value->subjectUser->grade_minimum;
                // $data[$value->student->student_name][$value->subjectUserThrough->subject_name]['subject_user_id'] = $value->subject_user_id;
                $data[$value->student->student_name][$value->subjectUserThrough->subject_name]['is_curiculum_basic'] = $value->is_curiculum_basic;
            }
            
            // Count avg based on the $data
            $newData = Helper::reportSheetCalculateAverage($data);
            // dd($data, $newData);

            // dd($data['Tyndale James Shumaker'], $newData['Tyndale James Shumaker']);
            // Sort by student name
            // ksort($newData);

            // GET THE PAS 
            $StudentSemesterEvaluation = StudentSemesterEvaluation::with('subjectUserThrough','student')
            ->whereIn('student_id',$studentIds)
            ->whereIn('subject_user_id',array_unique($assessments->pluck('subject_user_id')->toArray()))
            ->withoutGlobalScope('subjectUser')
            ->get();

            // Set the KKM
            foreach ($newData as $newDataKey => $newDataValue) {
                
                foreach ($newDataValue as $key => $value) {
                    // $newData[$newDataKey][$key]['KKM'] = $data[$newDataKey][$key]['KKM'];
                    $newData[$newDataKey][$key]['PAS'] = null;
                    // $newData[$newDataKey][$key]['subject_user_id'] = $data[$newDataKey][$key]['subject_user_id'] ; //this is exist for description in this case we dont need the description
                    $newData[$newDataKey][$key]['is_curiculum_basic'] = $data[$newDataKey][$key]['is_curiculum_basic'];

                    if($StudentSemesterEvaluation->where('student.student_name',$newDataKey)->where('subjectUserThrough.subject_name',$key)->first()){
                        $newData[$newDataKey][$key]['PAS'] = $StudentSemesterEvaluation->where('student.student_name',$newDataKey)->where('subjectUserThrough.subject_name',$key)->first()->grading;
                    }
                }
            }

            $getSchoolSettings = SchoolSetting::first();
            $avgDiv = ($getSchoolSettings->sumatif_avg/100);
            $PASDiv = ($getSchoolSettings->pas_avg/100);


            // Check apakah semua anak sudah memiliki semua nilai dimapel?,semua anak harus sama jumlah mapelnya dan urutannya harussama,lakukan pengecekan agar tidak salah menepatkan nilai di tabel nanti
            $firstCount = 0;
            $firstArrayData = null;
            foreach ($newData as $key => $value) {
                if ($key === array_key_first($newData)) {
                    $firstCount = count($value);
                    $firstArrayData = $value;
                }
                if(count($value) != $firstCount){
                    // $differences = array_diff_assoc(array_keys($firstArrayData), array_keys($value));

                    $getArrayDifKey = Helper::getArrayDifKey($value, $firstArrayData);

                    $separ = count($value) > count($firstArrayData) ? 'memiliki nilai di mapel '. implode(',', $getArrayDifKey) : 'tidak memiliki nilai di beberapa mapel ';
                    Notification::make()
                        ->danger()
                        ->persistent()
                        ->title('<strong>'.$key.'</strong> '.$separ.'</strong>. <br><br>Daftar mapel <strong>'.$key.'</strong>:<br><ol style="list-style:auto!important"><li>'.implode('</li><li>',array_keys($value)).'</li></ol>')
                        // ->title('<strong>'.$key.'</strong> '.$separ.' <strong>'. implode(',', $differences).'</strong>. <br><br>Daftar mapel <strong>'.$key.'</strong>:<br><ol style="list-style:auto!important"><li>'.implode('</li><li>',array_keys($value)).'</li></ol><br> Silahkan menghapus salah satu nilai pada mapel di atas/menambahkan mapel yang hanya dimiliki siswa ini kepada siswa lain')
                        ->send();
                    // If this ishappen stopthe prosses
                    return back();
                }
            }

            $finalNewData = [];
            $tableHeader = ['No','Nama Siswa'];

            
            $schoolCurriculum = [];
            foreach ($newData as $studentKey => $studentValue) {
                foreach ($studentValue as $key => $value) { 
                    if($newData[$studentKey][$key]['is_curiculum_basic']  == 0){
                        // $schoolCurriculum[$studentKey][$key] = $newData[$studentKey][$key];
                        $finalNewData[$studentKey][$key] = $newData[$studentKey][$key];
                    }
                }
            }


            $basicCurriculum = [];
            foreach ($newData as $studentKey => $studentValue) {
                foreach ($studentValue as $key => $value) { 
                    if($newData[$studentKey][$key]['is_curiculum_basic']){
                        // $basicCurriculum[$studentKey][$key] = $newData[$studentKey][$key];
                        $finalNewData[$studentKey][$key] = $newData[$studentKey][$key];
                    }
                }
            }

            foreach ($finalNewData as $studentKey => $studentValue) {
                if($studentKey === array_key_first($finalNewData)){
                    foreach ($studentValue as $key => $value) {
                        array_push($tableHeader,$key);
                    }
                }
            }
            array_push($tableHeader,'Rata-rata Akademik','Rata-rata Karakter','Nilai Akhir','Ranking');
            
            $getStudentCharacter = self::generateCharacterAvg($studentIds,$classroom->school_year_id,$classroom->school_term_id);
            
            Notification::make()
                ->title('Saved successfully')
                ->icon('heroicon-o-document-text')
                ->iconColor('success')
                ->send();

            return Excel::download(new ReportSheetExport(compact('tableHeader','finalNewData','PASDiv','avgDiv','getStudentCharacter')), 'report_sheet.xlsx');
        } catch (\Throwable $th) {
            DB::rollback();
            Notification::make()
                ->danger()
                ->title($th->getMessage())
                ->send();
        }
    }

    public static function generateCharacterAvg(array $studentIds, $school_year_id,$school_term_id){
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
                'student_id',
            )
            ->whereIn('student_id', $studentIds)
            ->where('school_year_id', $school_year_id)
            ->where('school_term_id', $school_term_id)
            ->groupBy( 'habits.aspect_id','habit_id','week','home','school','student_id')
            ->orderBy('habits.id', 'asc') // Order by the sort_order column from subject_users table
            ->orderBy('week','asc') // Order by the maximum grading
            ->get();

        // return $characterReports->first();
        // Group By Subject
        $activeData = [];
        foreach ($characterReports as $value) {
            $data[$value->student->student_name][$value->habit->aspect->name][$value->habit->name][$value->week] = ['home' => $value->home,'school' => $value->school];
        }
        return $data;

    }
}