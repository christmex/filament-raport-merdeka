<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Assessment;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\SubjectUser;
use App\Models\SubjectGroup;
use App\Models\TopicSetting;
use Illuminate\Http\Request;
use App\Models\SchoolSetting;
use App\Models\CharacterReport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StudentClassroom;
use App\Exports\GradeSheetExport;
use App\Exports\ReportSheetExport;
use App\Models\SubjectDescription;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AssessmentMethodSetting;
use Filament\Notifications\Notification;
use App\Models\StudentSemesterEvaluation;
use Masterminds\HTML5\Parser\CharacterReference;

class PrintController extends Controller
{
    public function print_raport_cover(Student $student){
        if(auth()->guest()){
            abort(404,'Login First');
        }

        $pdf = Pdf::loadView('print-raport-cover', compact('student'))->setPaper(array(0,0,612.283,935.433), 'portrait');//convert mm to point
        return $pdf->stream('print-raport-cover.pdf');
        // return $pdf->render();

        // return view('print-raport-cover',compact('student'));
    }
    public function print_raport(Student $student, Request $request){
        if(auth()->guest()){
            abort(404,'Login First');
        }

        $print_progress_report_date = $request->print_progress_report_date;
        $data = [];
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
        ->where('student_id', $student->id)
        // ->where('subject_users.school_year_id', SchoolYear::active())
        // ->where('subject_users.school_term_id', SchoolTerm::active())
        // ->where('subject_users.school_year_id', auth()->user()->activeHomeroom->first()->school_year_id)
        // ->where('subject_users.school_term_id', auth()->user()->activeHomeroom->first()->school_term_id)
        ->where('subject_users.school_year_id', $request->school_year_id)
        ->where('subject_users.school_term_id', $request->school_term_id)
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

        // Group By Subject
        $activeData = [];
        foreach ($assessments as $key => $value) {
            $data[$value->subjectUserThrough->subject_name][$value->topicSetting->id][$value->assessmentMethodSetting->assessment_method_setting_name] = ['grading' => $value->max_grading];
            $data[$value->subjectUserThrough->subject_name]['KKM'] = $value->subjectUser->grade_minimum;
            $data[$value->subjectUserThrough->subject_name]['subject_user_id'] = $value->subject_user_id;
            $data[$value->subjectUserThrough->subject_name]['is_curiculum_basic'] = $value->is_curiculum_basic;
            $data[$value->subjectUserThrough->subject_name]['subject_group_name'] = $value->name;
        }

        // dd($data);
        // dd($assessments->whereIn('subject_user_id',[78,46])->toArray());
        
        // Count avg based on the $data
        // $newData = Helper::calculateAverage($data);
        $newData = Helper::reportSheetCalculateAverage([$student->student_name => $data]);
        if(count($newData)){
            $newData = $newData[$student->student_name];
        }
        
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
            $newData[$key]['subject_group_name'] = $data[$key]['subject_group_name'] ;

            if($StudentSemesterEvaluation->where('subjectUserThrough.subject_name',$key)->first()){
                $newData[$key]['PAS'] =$StudentSemesterEvaluation->where('subjectUserThrough.subject_name',$key)->first()->grading;
            }
        }

        // get the min and max 
        $minMaxArray = [];
        foreach ($avgPerTopic as $subject => $topics) {
            
            $newData[$subject]['minMax_topic_id'] = [
                // Helper::getKeyByValue($topics, min($topics)) => min($topics),
                // Helper::getKeyByValue($topics, max($topics)) => max($topics)
                Helper::getKeyByValue($topics, max($topics)) => max($topics),
                Helper::getKeyByValue($topics, min($topics)) => min($topics)
            ];
        }
        // get the min and max 


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
        // dd(array_unique($assessments->pluck('subjectUser.subject.subjectGroup.id')->toArray()));

        $getSubjectGroup = SubjectGroup::all();

        // dd($basicCurriculum,$schoolCurriculum, $newData);
        // return view('print-raport',compact('student','basicCurriculum','schoolCurriculum','avgDiv','PASDiv','subjectDescription'));

    
        // ->where('range_start', '<=', 84)
        // ->where('range_end', '>=', 84)
        // ->first());
        // dd(array_unique($topicSettingIds));

        // $pdf = Pdf::loadView('print-raport-cover', compact('student'))->setPaper(array(0,0,612.283,935.433), 'portrait');//convert mm to point
        // // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,595.28,935.433), 'portrait');//convert mm to point
        // return $pdf->stream('print-raport-cover.pdf');

        // dd($basicCurriculum, $schoolCurriculum);
        // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,609.4488,935.433), 'portrait');
        // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,612.283,935.433), 'portrait');//convert mm to point 216 mm
        $pdf = Pdf::loadView('print-raport', compact('getSubjectGroup','student','basicCurriculum','schoolCurriculum','avgDiv','PASDiv','subjectDescription','print_progress_report_date'))->setPaper(array(0,0,609.449,935.433), 'portrait');//convert mm to point
        // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,595.28,935.433), 'portrait');//convert mm to point
        return $pdf->stream('print-raport.pdf');

        


        
    }

    public function print_grade_sheet(SubjectUser $subjectUser){
        // Check if the user is autheticated
        if(auth()->guest()){
            abort(404,'Login First');
        }


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
        return Excel::download(new GradeSheetExport(compact('data','thead','assessmentMethodSetting','totalTopic','dataPAS','subjectDescription','grade_minimum','avgDiv','PASDiv')), 'grade_sheet - '.$subjectUser->subject->subject_name.' kelas '.$subjectUser->classroom->school_level.' '.$subjectUser->classroom->classroom_name.'.xlsx');

        // return view('exports.grade-sheet', compact('data','thead','assessmentMethodSetting','totalTopic','dataPAS','subjectDescription','grade_minimum','avgDiv','PASDiv'));

    }
    
    public  function print_report_sheet_for_teacher(Classroom $classroom){
        if(auth()->guest()){
            abort(404,'Login First');
        }

        $classroom_id = $classroom->id;
        $classroom_name = $classroom->classroom_name;

        $data = [];

        $StudentClassroom = StudentClassroom::query()
            ->where('school_year_id',SchoolYear::activeId())
            ->where('school_term_id',SchoolTerm::activeId())
            ->where('classroom_id',$classroom_id)
            ->get()
            ->pluck('student_id')
            ->toArray();

        $studentIds = Student::whereIn('id',$StudentClassroom)
                        ->get()
                        ->pluck('id')
                        ->toArray();

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
            ->where('subject_users.school_year_id', SchoolYear::activeId())
            ->where('subject_users.school_term_id', SchoolTerm::activeId())
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
        // dd($finalNewData['Tyndale James Shumaker']);
        
        $getStudentCharacter = $this->generateCharacterAvg($studentIds);
        // if(count($studentIds) != count($getStudentCharacter)){
        //     Notification::make()
        //             ->danger()
        //             ->persistent()
        //             ->title('Please ask the main teacher to create character raport first then we can generate the report sheet')
        //             ->send();
        //         // If this ishappen stopthe prosses
        //         return back();
        // }

        return Excel::download(new ReportSheetExport(compact('tableHeader','finalNewData','PASDiv','avgDiv','getStudentCharacter')), 'report_sheet.xlsx');
        // $pdf = Pdf::loadView('print-report-sheet-for-teacher', compact('tableHeader','finalNewData','PASDiv','avgDiv','getStudentCharacter','classroom_name'))->setPaper('A4', 'landscape');//convert mm to point
        // return $pdf->stream('print-report-sheet-for-teacher.pdf');
    }

    // Deprecated
    public  function print_report_sheet(){
        if(auth()->guest()){
            abort(404,'Login First');
        }

        $data = [];

        $studentIds = Student::whereIn('id',StudentClassroom::where('school_year_id',auth()->user()->activeHomeroom->first()->school_year_id)
                    ->where('school_term_id',auth()->user()->activeHomeroom->first()->school_term_id)
                    ->where('classroom_id',auth()->user()->activeHomeroom->first()->classroom_id)
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
        ->where('subject_users.school_year_id', auth()->user()->activeHomeroom->first()->school_year_id)
        ->where('subject_users.school_term_id', auth()->user()->activeHomeroom->first()->school_term_id)
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
                $differences = array_diff_assoc(array_keys($firstArrayData), array_keys($value));
                $separ = count($value) > count($firstArrayData) ? 'memiliki nilai di mapel'. implode(',', $differences) : 'tidak memiliki nilai di beberapa mapel';
                Notification::make()
                    ->danger()
                    ->persistent()
                    ->title('<strong>'.$key.'</strong> '.$separ.'</strong>. <br><br>Daftar mapel <strong>'.$key.'</strong>:<br><ol style="list-style:auto!important"><li>'.implode('</li><li>',array_keys($value)).'</li></ol>')
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
        
        $getStudentCharacter = $this->generateCharacterAvg($studentIds);
        // if(count($studentIds) != count($getStudentCharacter)){
        //     Notification::make()
        //             ->danger()
        //             ->persistent()
        //             ->title('Please ask the main teacher to create character raport first then we can generate the report sheet')
        //             ->send();
        //         // If this ishappen stopthe prosses
        //         return back();
        // }

        return Excel::download(new ReportSheetExport(compact('tableHeader','finalNewData','PASDiv','avgDiv','getStudentCharacter')), 'report_sheet.xlsx');

        // $pdf = Pdf::loadView('print-report-sheet', compact('tableHeader','finalNewData','PASDiv','avgDiv','getStudentCharacter'))->setPaper('A4', 'landscape');//convert mm to point
        // return $pdf->download('print-report-sheet.pdf');
    }

    public function print_report_character(Student $student){
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
            ->where('school_year_id', auth()->user()->activeHomeroom->first()->school_year_id)
            ->where('school_term_id', auth()->user()->activeHomeroom->first()->school_term_id)

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
        $avgAcademic = $this->generateAcademyAvg([$student->id]);
        // dd(Helper::customRound($avgAcademic,10));
        $pdf = Pdf::loadView('print-report-character', compact('data','avgAcademic','student'))->setPaper(array(0,0,935.433,609.449), 'potrait');//convert mm to point
        return $pdf->download('print-report-character.pdf');

        // dd($data);

        
    }

    public function generateAcademyAvg($studentId){
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
        ->where('subject_users.school_year_id', auth()->user()->activeHomeroom->first()->school_year_id)
        ->where('subject_users.school_term_id', auth()->user()->activeHomeroom->first()->school_term_id)
        ->whereNotNull('grading')
        ->groupBy( 'subjects.is_curiculum_basic','assessment_method_setting_id', 'subject_user_id', 'topic_setting_id','student_id')
        ->orderBy('subjects.sort_order', 'asc') // Order by the sort_order column from subject_users table
        ->orderBy('subjects.subject_name', 'asc') // Order by the sort_order column from subject_users table
        ->orderBy('students.student_name', 'asc')
        ->orderByDesc('max_grading') // Order by the maximum grading
        ->withoutGlobalScope('subjectUser')
        ->get();

        // $assessments = Assessment::query()
        // ->with('assessmentMethodSetting', 'topicSetting', 'student', 'subjectUserThrough','subjectUser')
        // ->join('subject_users', 'assessments.subject_user_id', '=', 'subject_users.id')
        // ->join('subjects', 'subject_users.subject_id', '=', 'subjects.id') // Inner join another_table inside subject_users    
        // ->select(
        //     'subjects.is_curiculum_basic',
        //     'subject_user_id',
        //     'assessment_method_setting_id',
        //     'topic_setting_id',
        //     'student_id',
        //     DB::raw('AVG(grading) as max_grading')
        // )
        // ->whereIn('student_id', $studentId)
        // ->where('subject_users.school_year_id', auth()->user()->activeHomeroom->first()->school_year_id)
        // ->where('subject_users.school_term_id', auth()->user()->activeHomeroom->first()->school_term_id)
        // ->whereNotNull('grading')
        // ->groupBy( 'subjects.is_curiculum_basic','assessment_method_setting_id', 'subject_user_id', 'topic_setting_id','student_id')
        // ->orderBy('subjects.sort_order', 'asc') // Order by the sort_order column from subject_users table
        // ->orderByDesc('max_grading') // Order by the maximum grading
        // ->withoutGlobalScope('subjectUser')
        // ->get();

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
        // dd($avg);
        return array_sum($avg) / count($avg);

        // return compact('avg','finalNewData','PASDiv','avgDiv');
    }

    public function generateCharacterAvg(array $studentIds){
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
