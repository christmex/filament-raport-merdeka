<?php

namespace App\Helpers;

use Exception;
use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\SchoolSetting;
use App\Models\CharacterReport;
use App\Models\StudentClassroom;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Models\StudentSemesterEvaluation;


class Report {
    
    public static function generateReportSheet($classroom_id){

        $studentIds = Student::whereIn('id',StudentClassroom::where('school_year_id',SchoolYear::activeId())
                    ->where('school_term_id',SchoolTerm::activeId())
                    ->where('classroom_id',$classroom_id)
                    ->get()->pluck('student_id')->toArray())->get()->pluck('id')->toArray();

        $assessments = Assessment::query()
        ->with('assessmentMethodSetting', 'topicSetting', 'student', 'subjectUserThrough','subjectUser')
        ->join('subject_users', 'assessments.subject_user_id', '=', 'subject_users.id')
    
        ->join('subjects', 'subject_users.subject_id', '=', 'subjects.id') // Inner join another_table inside subject_users
    
        ->select(
            'subjects.is_curiculum_basic',
            'subject_user_id',
            'assessment_method_setting_id',
            'topic_setting_id',
            'student_id',
            DB::raw('AVG(grading) as max_grading')
        )
        ->whereIn('student_id', $studentIds)
        ->where('subject_users.school_year_id', SchoolYear::activeId())
        ->where('subject_users.school_term_id', SchoolTerm::activeId())
        ->whereNotNull('grading')
        ->groupBy( 'subjects.is_curiculum_basic','assessment_method_setting_id', 'subject_user_id', 'topic_setting_id','student_id')
        ->orderBy('subjects.sort_order', 'asc') // Order by the sort_order column from subject_users table
        ->orderBy('subjects.subject_name', 'asc') // Order by the sort_order column from subject_users table
        ->orderByDesc('max_grading') // Order by the maximum grading
        ->withoutGlobalScope('subjectUser')
        ->get();

        // Group By Subject
        $activeData = [];
        foreach ($assessments as $key => $value) {
            $data[$value->student->student_name][$value->subjectUserThrough->subject_name][$value->topicSetting->id][$value->assessmentMethodSetting->assessment_method_setting_name] = ['grading' => $value->max_grading];
            $data[$value->student->student_name][$value->subjectUserThrough->subject_name]['KKM'] = $value->subjectUser->grade_minimum;
            // $data[$value->student->student_name][$value->subjectUserThrough->subject_name]['subject_user_id'] = $value->subject_user_id;
            $data[$value->student->student_name][$value->subjectUserThrough->subject_name]['is_curiculum_basic'] = $value->is_curiculum_basic;
        }

        // Count avg based on the $data
        $newData = Helper::reportSheetCalculateAverage($data);
        // Sort by student name
        ksort($newData);

        // GET THE PAS 
        $StudentSemesterEvaluation = StudentSemesterEvaluation::with('subjectUserThrough')
        ->whereIn('student_id',$studentIds)
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
                // dd( Helper::getArrayDifKey($value, $firstArrayData),Helper::getIndexPosition($value,'Seni Budaya (Musik)'),$firstArrayData, $value, $newData, $key);

                // Jika nilai saat ini lebih besar tambahkan nilai yang lebih ke semua anak 
                if(count($value) > $firstCount){
                    $getArrayDifKey = Helper::getArrayDifKey($value, $firstArrayData); //array return
                    
                    foreach ($getArrayDifKey as $getArrayDifKey_value) {
                        $getIndexPosition = Helper::getIndexPosition($value,$getArrayDifKey_value);
                        foreach ($newData as $addNewSubjectKey => $addNewSubjectValue) {
                            if($key != $addNewSubjectKey){
                                if($newData[$addNewSubjectKey]){

                                }
                                $inserted = [
                                    $getArrayDifKey_value => [
                                        "AVG" => 0,
                                        "KKM" => 70,
                                        "PAS" => null,
                                        "is_curiculum_basic" => 0
                                    ]
                                ];
                                // dd($getIndexPosition);
                                array_splice( $newData[$addNewSubjectKey], $getIndexPosition, 0, $inserted ); // splice in at position 3
                                // $original is now a b c x d e


                                // $newData[$addNewSubjectKey] = 
                            }
                        }
                    }

                }else {
                    // Jika nilai saat ini lebih kecil, maka tambahkan nilai yang kurang
                    // $newData
                }



                // $differences = array_diff_assoc(array_keys($firstArrayData), array_keys($value));
                // $separ = count($value) > count($firstArrayData) ? 'memiliki nilai di mapel'. implode(',', $differences) : 'tidak memiliki nilai di beberapa mapel';
                // Notification::make()
                //     ->danger()
                //     ->persistent()
                //     ->title('<strong>'.$key.'</strong> '.$separ.'</strong>. <br><br>Daftar mapel <strong>'.$key.'</strong>:<br><ol style="list-style:auto!important"><li>'.implode('</li><li>',array_keys($value)).'</li></ol>')
                //     ->send();


                // If this ishappen stopthe prosses
                // dd($value);
                // throw new Exception("Error Processing Request", 1);
                
                // return back();
            }
        }
        dd($newData);
        $finalNewData = [];
        $tableHeader = ['No','Nama Siswa'];

        // separated school curriculum
        $schoolCurriculum = [];
        foreach ($newData as $studentKey => $studentValue) {
            foreach ($studentValue as $key => $value) { 
                if($newData[$studentKey][$key]['is_curiculum_basic']  == 0){
                    // $schoolCurriculum[$studentKey][$key] = $newData[$studentKey][$key];
                    $finalNewData[$studentKey][$key] = $newData[$studentKey][$key];
                }
            }
        }

        // separated basic curriculum 
        $basicCurriculum = [];
        foreach ($newData as $studentKey => $studentValue) {
            foreach ($studentValue as $key => $value) { 
                if($newData[$studentKey][$key]['is_curiculum_basic']){
                    // $basicCurriculum[$studentKey][$key] = $newData[$studentKey][$key];
                    $finalNewData[$studentKey][$key] = $newData[$studentKey][$key];
                }
            }
        }

        // create header
        foreach ($finalNewData as $studentKey => $studentValue) {
            if($studentKey === array_key_first($finalNewData)){
                foreach ($studentValue as $key => $value) {
                    array_push($tableHeader,$key);
                }
            }
        }
        // foreach ($schoolCurriculum as $studentKey => $studentValue) {
        //     if($studentKey === array_key_first($schoolCurriculum)){
        //         foreach ($studentValue as $key => $value) {
        //             array_push($tableHeader,$key);
        //         }
        //     }
        // }
        // foreach ($basicCurriculum as $studentKey => $studentValue) {
        //     if($studentKey === array_key_first($basicCurriculum)){
        //         foreach ($studentValue as $key => $value) {
        //             array_push($tableHeader,$key);
        //         }
        //     }
        // }
        array_push($tableHeader,'Rata-rata Akademik','Rata-rata Karakter','Nilai Akhir','Ranking');
        // dd($tableHeader,$finalNewData);
        
        $getStudentCharacter = self::generateCharacterAvg($studentIds);

        dd($finalNewData, $tableHeader);
        return compact('tableHeader','finalNewData','PASDiv','avgDiv','getStudentCharacter');

    }

    public static function generateCharacterAvg(array $studentIds){
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
            ->where('school_year_id', SchoolYear::activeId())
            ->where('school_term_id', SchoolTerm::activeId())
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