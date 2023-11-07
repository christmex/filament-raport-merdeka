<?php

use App\Models\StudentSemesterEvaluation;
use App\Models\User;
use App\Helpers\Helper;
use App\Http\Controllers\PrintController;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\SchoolYear;
use App\Models\SubjectUser;
use App\Models\TopicSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\AssessmentMethodSetting;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     dd(auth()->user()->getActiveSubjectIds());
//     dd(Auth()->user()->activeHomeroom->first());
//     return view('welcome');
// });

Route::get('/debug',function(){
    dd(Student::find(93)->active_classroom_name);
    // dd(StudentSemesterEvaluation::first()->student->active_classroom_name);
    // dd(SubjectUser::with('Subject')->ownSubject()->get()->pluck('Subject.subject_name','id'));
    // dd(Assessment::with('classroomSubjectUserThrough','subjectUserThrough')->get()->pluck('subjectUserThrough.subject_name'));
    // $user = User::find(auth()->id());

    // dd($user->can('download-backup'));
});

Route::get('/print-raport-cover/{student}',[PrintController::class,'print_raport_cover'])->name('students.print-raport-cover');
Route::get('/print-raport/{student}',[PrintController::class,'print_raport'])->name('students.print-raport');

// Route::get('/print-raport/{student}',function(Student $student){
//     if(auth()->guest()){
//         abort(404,'Login First');
//     }

//     return view('print-raport',compact('student'));
//     // return Pdf::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');

//     // $viewPath = view('print-raport')->getPath();

//     // $pdf = Pdf::loadFile($viewPath)->stream('download.pdf');


//     // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,609.4488,935.433), 'portrait');
//     // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,612.283,935.433), 'portrait');//convert mm to point 216 mm
//     $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,612.283,935.433), 'portrait');//convert mm to point
//     // $pdf = Pdf::loadView('print-raport', compact('student'))->setPaper(array(0,0,595.28,935.433), 'portrait');//convert mm to point
//     return $pdf->stream('print-raport.pdf');


// })->name('students.print-raport');

Route::get('/print/{student}', function(Student $student){
    if(auth()->guest()){
        abort(404,'Login First');
    }
    // dd(Assessment::all());
    // dd($student);
    // dd(AssessmentMethodSetting::find(1));
    // dd(TopicSetting::find(3));

    $format = [
        [
            'subject_name' => 'Matematika',
            'topic_1_tes_lisan' => 60,
            'topic_1_penugasan' => 60,
            'topic_1_kinerja' => 60,
            'topic_1_monthly_test' => 60,
            'topic_1_avg' => 60,

            'topic_2_tes_lisan' => 60,
            'topic_2_penugasan' => 60,
            'topic_2_kinerja' => 60,
            'topic_2_monthly_test' => 60,
            'topic_2_avg' => 60,

            'topic_3_tes_lisan' => 60,
            'topic_3_penugasan' => 60,
            'topic_3_kinerja' => 60,
            'topic_3_monthly_test' => 60,
            'topic_3_avg' => 60,
        ],
        [
            'subject_name' => 'Bahasa Indonesia',
            'topic_1_tes_lisan' => 60,
            'topic_1_penugasan' => 60,
            'topic_1_kinerja' => 60,
            'topic_1_monthly_test' => 60,
            'topic_1_avg' => 60,

            'topic_2_tes_lisan' => 60,
            'topic_2_penugasan' => 60,
            'topic_2_kinerja' => 60,
            'topic_2_monthly_test' => 60,
            'topic_2_avg' => 60,

            'topic_3_tes_lisan' => 60,
            'topic_3_penugasan' => 60,
            'topic_3_kinerja' => 60,
            'topic_3_monthly_test' => 60,
            'topic_3_avg' => 60,
        ]
    ];


    $dataList = [];


    // $assessments = Assessment::query()
    //     ->with('assessmentMethodSetting','topicSetting','student','subjectUserThrough')
    //     ->where('student_id', $student->id)
    //     ->where('grading','!=',NULL)
    //     ->orderByDesc('grading')
    //     // ->withoutGlobalScope('subjectUser')
    //     ->get();

    // $assessments = Assessment::query()
    //     ->select('assessment_method_setting_id', 'subject_user_id', 'topic_setting_id', DB::raw('MAX(grading) as max_grading'))
    //     ->with('assessmentMethodSetting', 'topicSetting', 'student', 'subjectUserThrough')
    //     ->where('student_id', $student->id)
    //     ->whereNotNull('grading') // Check for non-null values
    //     ->groupBy('assessment_method_setting_id', 'subject_user_id', 'topic_setting_id')
    //     ->orderByDesc('max_grading') // Order by the maximum grading
    //     ->get();

    // $assessments = Assessment::query()
    // ->with('assessmentMethodSetting', 'topicSetting', 'student', 'subjectUserThrough')
    // ->select('assessment_method_setting_id', 'subject_user_id', 'topic_setting_id', DB::raw('MAX(grading) as max_grading'))
    // ->where('student_id', $student->id)
    // ->whereNotNull('grading') // Check for non-null values
    // ->groupBy('assessment_method_setting_id', 'subject_user_id', 'topic_setting_id')
    // ->orderByDesc('max_grading') // Order by the maximum grading
    // ->withoutGlobalScope('subjectUser')
    // ->get();

    $assessments = Assessment::query()
    ->with('assessmentMethodSetting', 'topicSetting', 'student', 'subjectUserThrough')
    ->join('subject_users', 'assessments.subject_user_id', '=', 'subject_users.id')

    ->join('subjects', 'subject_users.subject_id', '=', 'subjects.id') // Inner join another_table inside subject_users

    ->select(
        'subjects.is_curiculum_basic',
        'assessment_method_setting_id',
        'subject_user_id',
        'topic_setting_id',
        // DB::raw('(SELECT is_curiculum_basic FROM subjects) as is_curiculum_basi'), // Replace with your actual condition
        // DB::raw('(SELECT is_curiculum_basic FROM is_curiculum_basic WHERE some_condition) as is_curriculum_basic_column') // Replace with your actual condition
        // DB::raw('MAX(grading) as max_grading')
        DB::raw('AVG(grading) as max_grading')
    )
    ->where('student_id', $student->id)
    ->whereNotNull('grading')
    // ->groupBy('assessment_method_setting_id', 'subject_user_id', 'topic_setting_id')
    ->groupBy('subjects.is_curiculum_basic', 'assessment_method_setting_id', 'subject_user_id', 'topic_setting_id')
    ->orderBy('subjects.sort_order', 'asc') // Order by the sort_order column from subject_users table
    ->orderByDesc('max_grading') // Order by the maximum grading
    ->withoutGlobalScope('subjectUser')
    ->get();

    $dataPublicCur = Helper::generateRaportData($assessments->where('is_curiculum_basic',false));
    $dataBasicCur = Helper::generateRaportData($assessments->where('is_curiculum_basic',true));

    // dd($dataPublicCur,$assessments->where('is_curiculum_basic',true));


    foreach ($assessments as $key => $value) {
        // Cek apakah subject ini sudah ada di data array?
        if(Helper::searchValueOnKey($dataList, 'subject_name',$value->subjectUserThrough->subject_name)){

            // Yes
            $initData = Helper::findSubjectByName($dataList,$value->subjectUserThrough->subject_name);
            if(!($initData[1]['model_value']['assessment_method_setting_id'] == $value->assessment_method_setting_id && $initData[1]['model_value']['topic_setting_id'] == $value->topic_setting_id)){
                // jika beda, maka kita harus tambahkan ke key yang bersangkutan, 

                $dataList[$initData[0]]['topic_1_tes_lisan'] = $dataList[$initData[0]]['topic_1_tes_lisan'] ?? Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_1_tes_lisan', 1, 1);
                $dataList[$initData[0]]['topic_1_penugasan'] = $dataList[$initData[0]]['topic_1_penugasan'] ??  Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_1_penugasan', 1, 2) ;
                $dataList[$initData[0]]['topic_1_kinerja'] = $dataList[$initData[0]]['topic_1_kinerja'] ??  Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_1_kinerja', 1, 3) ;
                $dataList[$initData[0]]['topic_1_monthly_test'] = $dataList[$initData[0]]['topic_1_monthly_test'] ??  Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_1_monthly_test', 1, 4) ;
                $dataList[$initData[0]]['topic_1_avg'] =  Helper::topicAvg([
                    $dataList[$initData[0]]['topic_1_tes_lisan'],
                    $dataList[$initData[0]]['topic_1_penugasan'],
                    $dataList[$initData[0]]['topic_1_kinerja'],
                    $dataList[$initData[0]]['topic_1_monthly_test']
                ]);

               $dataList[$initData[0]]['topic_2_tes_lisan'] = $dataList[$initData[0]]['topic_2_tes_lisan'] ?? Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_2_tes_lisan', 2, 1) ;
               $dataList[$initData[0]]['topic_2_penugasan'] = $dataList[$initData[0]]['topic_2_penugasan'] ?? Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_2_penugasan', 2, 2) ;
               $dataList[$initData[0]]['topic_2_kinerja'] = $dataList[$initData[0]]['topic_2_kinerja'] ?? Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_2_kinerja', 2, 3) ;
               $dataList[$initData[0]]['topic_2_monthly_test'] = $dataList[$initData[0]]['topic_2_monthly_test'] ?? Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_2_monthly_test', 2, 4) ;
               $dataList[$initData[0]]['topic_2_avg'] = Helper::topicAvg([
                    $dataList[$initData[0]]['topic_2_tes_lisan'],
                    $dataList[$initData[0]]['topic_2_penugasan'],
                    $dataList[$initData[0]]['topic_2_kinerja'],
                    $dataList[$initData[0]]['topic_2_monthly_test']
                ]);

                $dataList[$initData[0]]['topic_3_tes_lisan'] = $dataList[$initData[0]]['topic_3_tes_lisan'] ?? Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_3_tes_lisan', 3, 1) ;
                $dataList[$initData[0]]['topic_3_penugasan'] = $dataList[$initData[0]]['topic_3_penugasan'] ?? Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_3_penugasan', 3, 2) ;
                $dataList[$initData[0]]['topic_3_kinerja'] = $dataList[$initData[0]]['topic_3_kinerja'] ?? Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_3_kinerja', 3, 3) ;
                $dataList[$initData[0]]['topic_3_monthly_test'] = $dataList[$initData[0]]['topic_3_monthly_test'] ?? Helper::CheckAssessment($value,$dataList[$initData[0]], 'topic_3_monthly_test', 3, 4) ;
                $dataList[$initData[0]]['topic_3_avg'] = Helper::topicAvg([
                    $dataList[$initData[0]]['topic_3_tes_lisan'],
                    $dataList[$initData[0]]['topic_3_penugasan'],
                    $dataList[$initData[0]]['topic_3_kinerja'],
                    $dataList[$initData[0]]['topic_3_monthly_test']
                ]);

                $dataList[$initData[0]]['model_value'] = $value->toArray();

                
            }

            // if($key== 2){
            //     dd($value, $initData[1]['model_value']['assessment_method_setting_id'], $value->assessment_method_setting_id, $value->topic_setting_id,$initData[1]['model_value']['topic_setting_id']);
            // }
        }else {
            // No,init the data
            $dataList[] = [
                'subject_name' => $value->subjectUserThrough->subject_name,
                'topic_1_tes_lisan' => Helper::CheckAssessment($value,$dataList, 'topic_1_tes_lisan', 1, 1),
                'topic_1_penugasan' => Helper::CheckAssessment($value,$dataList, 'topic_1_penugasan', 1, 2),
                'topic_1_kinerja' => Helper::CheckAssessment($value,$dataList, 'topic_1_kinerja', 1, 3),
                'topic_1_monthly_test' => Helper::CheckAssessment($value,$dataList, 'topic_1_monthly_test', 1, 4),
                'topic_1_avg' => Helper::topicAvg([
                    Helper::CheckAssessment($value,$dataList, 'topic_1_tes_lisan', 1, 1),
                    Helper::CheckAssessment($value,$dataList, 'topic_1_penugasan', 1, 2),
                    Helper::CheckAssessment($value,$dataList, 'topic_1_kinerja', 1, 3),
                    Helper::CheckAssessment($value,$dataList, 'topic_1_monthly_test', 1, 4),
                ]),


                'topic_2_tes_lisan' => Helper::CheckAssessment($value,$dataList, 'topic_2_tes_lisan', 2, 1),
                'topic_2_penugasan' => Helper::CheckAssessment($value,$dataList, 'topic_2_penugasan', 2, 2),
                'topic_2_kinerja' => Helper::CheckAssessment($value,$dataList, 'topic_2_kinerja', 2, 3),
                'topic_2_monthly_test' => Helper::CheckAssessment($value,$dataList, 'topic_2_monthly_test', 2, 4),
                'topic_2_avg' => Helper::topicAvg([
                    Helper::CheckAssessment($value,$dataList, 'topic_2_tes_lisan', 2, 1),
                    Helper::CheckAssessment($value,$dataList, 'topic_2_penugasan', 2, 2),
                    Helper::CheckAssessment($value,$dataList, 'topic_2_kinerja', 2, 3),
                    Helper::CheckAssessment($value,$dataList, 'topic_2_monthly_test', 2, 4),
                ]),


                'topic_3_tes_lisan' => Helper::CheckAssessment($value,$dataList, 'topic_3_tes_lisan', 3, 1),
                'topic_3_penugasan' => Helper::CheckAssessment($value,$dataList, 'topic_3_penugasan', 3, 2),
                'topic_3_kinerja' => Helper::CheckAssessment($value,$dataList, 'topic_3_kinerja', 3, 3),
                'topic_3_monthly_test' => Helper::CheckAssessment($value,$dataList, 'topic_3_monthly_test', 3, 4),
                'topic_3_avg' => Helper::topicAvg([
                    Helper::CheckAssessment($value,$dataList, 'topic_3_tes_lisan', 3, 1),
                    Helper::CheckAssessment($value,$dataList, 'topic_3_penugasan', 3, 2),
                    Helper::CheckAssessment($value,$dataList, 'topic_3_kinerja', 3, 3),
                    Helper::CheckAssessment($value,$dataList, 'topic_3_monthly_test', 3, 4),
                ]),

                'model_value' => $value->toArray()
            ];
        }
    }

    // dd($dataList,$dataPublicCur,$assessments->where('is_curiculum_basic',true));

    $topicSettings = TopicSetting::take(3)->get();
    if(request('detailed')){
        return view('print',compact('dataList','student','topicSettings'));
    }else {
        return view('printv2',compact('dataPublicCur','dataBasicCur','student','topicSettings'));
    }

    dd($dataList, $assessments);



















    
    foreach ($assessments as $key => $value) {
        // bukan cek subject_name karna subject name bisa beberapa kali loop, tapi yang di cek adalah assessment method setting, jadi biarkan subject name nya sama, tapi jika assemsne tmethod nya sama maka tidak usah dilakukan apapun, cukup ambi nilai terbaik, dan jika assemenet method setting beda maka boleh dimasukkan ke data, lakukan seperti itu
        if(!Helper::searchValueOnKey($dataList, 'subject_name',$value->subjectUserThrough->subject_name)){
            $dataList[] = [
                'subject_name' => $value->subjectUserThrough->subject_name,
                'topic_1_tes_lisan' => Helper::CheckAssessment($value,$dataList, 'topic_1_tes_lisan', 1, 1),
                'topic_1_penugasan' => Helper::CheckAssessment($value,$dataList, 'topic_1_penugasan', 1, 2),
                'topic_1_kinerja' => Helper::CheckAssessment($value,$dataList, 'topic_1_kinerja', 1, 3),
                'topic_1_monthly_test' => Helper::CheckAssessment($value,$dataList, 'topic_1_monthly_test', 1, 4),
                'topic_1_avg' => Helper::topicAvg([
                    Helper::CheckAssessment($value,$dataList, 'topic_1_tes_lisan', 1, 1),
                    Helper::CheckAssessment($value,$dataList, 'topic_1_penugasan', 1, 2),
                    Helper::CheckAssessment($value,$dataList, 'topic_1_kinerja', 1, 3),
                    Helper::CheckAssessment($value,$dataList, 'topic_1_monthly_test', 1, 4),
                ]),

                'topic_2_tes_lisan' => Helper::CheckAssessment($value,$dataList, 'topic_2_tes_lisan', 2, 1),
                'topic_2_penugasan' => Helper::CheckAssessment($value,$dataList, 'topic_2_penugasan', 2, 2),
                'topic_2_kinerja' => Helper::CheckAssessment($value,$dataList, 'topic_2_kinerja', 2, 3),
                'topic_2_monthly_test' => Helper::CheckAssessment($value,$dataList, 'topic_2_monthly_test', 2, 4),
                'topic_2_avg' => Helper::topicAvg([
                    Helper::CheckAssessment($value,$dataList, 'topic_2_tes_lisan', 2, 1),
                    Helper::CheckAssessment($value,$dataList, 'topic_2_penugasan', 2, 2),
                    Helper::CheckAssessment($value,$dataList, 'topic_2_kinerja', 2, 3),
                    Helper::CheckAssessment($value,$dataList, 'topic_2_monthly_test', 2, 4),
                ]),

                'topic_3_tes_lisan' => Helper::CheckAssessment($value,$dataList, 'topic_3_tes_lisan', 3, 1),
                'topic_3_penugasan' => Helper::CheckAssessment($value,$dataList, 'topic_3_penugasan', 3, 2),
                'topic_3_kinerja' => Helper::CheckAssessment($value,$dataList, 'topic_3_kinerja', 3, 3),
                'topic_3_monthly_test' => Helper::CheckAssessment($value,$dataList, 'topic_3_monthly_test', 3, 4),
                'topic_3_avg' => Helper::topicAvg([
                    Helper::CheckAssessment($value,$dataList, 'topic_3_tes_lisan', 3, 1),
                    Helper::CheckAssessment($value,$dataList, 'topic_3_penugasan', 3, 2),
                    Helper::CheckAssessment($value,$dataList, 'topic_3_kinerja', 3, 3),
                    Helper::CheckAssessment($value,$dataList, 'topic_3_monthly_test', 3, 4),
                ]),
                'model_value' => $value->toArray()
            ];
            // dd($value);
        }else {
            // jika mapel sudah ada,berarti ambil nilai yang beda aja, ambilk assemeend method setting yang beda
            $initData = Helper::findSubjectByName($dataList,$value->subjectUserThrough->subject_name);
            if(!($initData['model_value']['assessment_method_setting_id'] == $value->assessment_method_setting_id)){
                // jika beda, maka kita harus tambahkan ke key yang bersangkutan, 
                $initData['topic_1_tes_lisan'] = Helper::CheckAssessment($value,$initData, 'topic_1_tes_lisan', 1, 1) ?? Helper::CheckAssessment($value,$initData, 'topic_1_tes_lisan', 1, 1);
                $initData['topic_1_penugasan'] = Helper::CheckAssessment($value,$initData, 'topic_1_penugasan', 1, 2) ?? Helper::CheckAssessment($value,$initData, 'topic_1_penugasan', 1, 2);
                $initData['topic_1_kinerja'] = Helper::CheckAssessment($value,$initData, 'topic_1_kinerja', 1, 3) ?? Helper::CheckAssessment($value,$initData, 'topic_1_kinerja', 1, 3);
                $initData['topic_1_monthly_test'] = Helper::CheckAssessment($value,$initData, 'topic_1_monthly_test', 1, 4) ?? Helper::CheckAssessment($value,$initData, 'topic_1_monthly_test', 1, 4);
                $initData['topic_1_avg'] = Helper::topicAvg([
                    Helper::CheckAssessment($value,$initData, 'topic_1_tes_lisan', 1, 1),
                    Helper::CheckAssessment($value,$initData, 'topic_1_penugasan', 1, 2),
                    Helper::CheckAssessment($value,$initData, 'topic_1_kinerja', 1, 3),
                    Helper::CheckAssessment($value,$initData, 'topic_1_monthly_test', 1, 4),
                ]) ?? Helper::topicAvg([
                    Helper::CheckAssessment($value,$initData, 'topic_1_tes_lisan', 1, 1),
                    Helper::CheckAssessment($value,$initData, 'topic_1_penugasan', 1, 2),
                    Helper::CheckAssessment($value,$initData, 'topic_1_kinerja', 1, 3),
                    Helper::CheckAssessment($value,$initData, 'topic_1_monthly_test', 1, 4),
                ]);

                $initData['topic_2_tes_lisan'] = Helper::CheckAssessment($value,$initData, 'topic_2_tes_lisan', 2, 1) ?? Helper::CheckAssessment($value,$initData, 'topic_2_tes_lisan', 2, 1);
                $initData['topic_2_penugasan'] = Helper::CheckAssessment($value,$initData, 'topic_2_penugasan', 2, 2) ?? Helper::CheckAssessment($value,$initData, 'topic_2_penugasan', 2, 2);
                $initData['topic_2_kinerja'] = Helper::CheckAssessment($value,$initData, 'topic_2_kinerja', 2, 3) ?? Helper::CheckAssessment($value,$initData, 'topic_2_kinerja', 2, 3);
                $initData['topic_2_monthly_test'] = Helper::CheckAssessment($value,$initData, 'topic_2_monthly_test', 2, 4) ?? Helper::CheckAssessment($value,$initData, 'topic_2_monthly_test', 2, 4);
                $initData['topic_2_avg'] = Helper::topicAvg([
                    Helper::CheckAssessment($value,$initData, 'topic_2_tes_lisan', 2, 1),
                    Helper::CheckAssessment($value,$initData, 'topic_2_penugasan', 2, 2),
                    Helper::CheckAssessment($value,$initData, 'topic_2_kinerja', 2, 3),
                    Helper::CheckAssessment($value,$initData, 'topic_2_monthly_test', 2, 4),
                ]) ?? Helper::topicAvg([
                    Helper::CheckAssessment($value,$initData, 'topic_2_tes_lisan', 2, 1),
                    Helper::CheckAssessment($value,$initData, 'topic_2_penugasan', 2, 2),
                    Helper::CheckAssessment($value,$initData, 'topic_2_kinerja', 2, 3),
                    Helper::CheckAssessment($value,$initData, 'topic_2_monthly_test', 2, 4),
                ]);

                $initData['topic_3_tes_lisan'] = Helper::CheckAssessment($value,$initData, 'topic_3_tes_lisan', 3, 1) ?? Helper::CheckAssessment($value,$initData, 'topic_3_tes_lisan', 3, 1);
                $initData['topic_3_penugasan'] = Helper::CheckAssessment($value,$initData, 'topic_3_penugasan', 3, 2) ?? Helper::CheckAssessment($value,$initData, 'topic_3_penugasan', 3, 2);
                $initData['topic_3_kinerja'] = Helper::CheckAssessment($value,$initData, 'topic_3_kinerja', 3, 3) ?? Helper::CheckAssessment($value,$initData, 'topic_3_kinerja', 3, 3);
                $initData['topic_3_monthly_test'] = Helper::CheckAssessment($value,$initData, 'topic_3_monthly_test', 3, 4) ?? Helper::CheckAssessment($value,$initData, 'topic_3_monthly_test', 3, 4);
                $initData['topic_3_avg'] = Helper::topicAvg([
                    Helper::CheckAssessment($value,$initData, 'topic_3_tes_lisan', 3, 1),
                    Helper::CheckAssessment($value,$initData, 'topic_3_penugasan', 3, 2),
                    Helper::CheckAssessment($value,$initData, 'topic_3_kinerja', 3, 3),
                    Helper::CheckAssessment($value,$initData, 'topic_3_monthly_test', 3, 4),
                ]) ?? Helper::topicAvg([
                    Helper::CheckAssessment($value,$initData, 'topic_3_tes_lisan', 3, 1),
                    Helper::CheckAssessment($value,$initData, 'topic_3_penugasan', 3, 2),
                    Helper::CheckAssessment($value,$initData, 'topic_3_kinerja', 3, 3),
                    Helper::CheckAssessment($value,$initData, 'topic_3_monthly_test', 3, 4),
                ]);

                // dd($initData,$key, $value);
            }
            // dd(Helper::findSubjectByName($dataList,$value->subjectUserThrough->subject_name),$value);
            // 
            // dd($value->assessmentMethodSetting->assessment_method_setting_name);
            
        }
    }
    dd($dataList);
    // return view('print',['data'=>$dataList, 'student',$student]);
    return view('print',compact('dataList','student'));

    // dd($dataList, $assessments);







    $assessments = Assessment::select(
        'student_id',
        'subject_user_id',
        'topic_setting_id',
        DB::raw('SUM(grading) as total_grading') // Calculate the sum of grading
    )
        ->groupBy('student_id')
        ->groupBy('subject_user_id')
        ->groupBy('topic_setting_id')
        ->get();
    dd($assessments);
    
    $format = [
        [
            'student_id' => 1,
            'student_name' => 'Jonathan',
            'student_nis' => 123,
            'student_nisn' => 1235,
            'classroom' => '4 Caleb 2',
            'term' => 'Genap',
            'schoolYear' => '2023-2024',
            'subjects' => [
                [
                    'subject_id' => 1,
                    'subject_name' => 'Matematika',
                    'topics' => [
                        1 => [
                            'topic_id' => 1,
                            'topic_setting_name' => 'Topic 1',
                            'assessment_method' => [
                                'Tes Lisan' => null,
                                'Penugasan' => null,
                                'Kinerja' => null,
                                'Monthly test' => null,
                                'Rata-rata' => null,
                            ]
                        ]
                    ]
                ]
            ]
        ]
        
    ];
    $originalArray = Assessment::with('assessmentMethodSetting','topicSetting','student','subjectUserThrough')->whereIn('student_id', [2])->orderByDesc('grading')->get()->toArray();
    $result = [];

    foreach ($originalArray as $item) {
        $subjectName = $item['subject_user_through']['subject_name'];
        $resultItem = ['subject_name' => $subjectName];

        for ($i = 1; $i <= 3; $i++) {
            $resultItem["topic_{$i}_tes_lisan"] = $item['grading'];
            $resultItem["topic_{$i}_penugasan"] = $item['grading'];
            $resultItem["topic_{$i}_kinerja"] = $item['grading'];
            $resultItem["topic_{$i}_monthly_test"] = $item['grading'];
            $resultItem["topic_{$i}_avg"] = $item['grading'];
        }

        $result[] = $resultItem;
    }
    dd($result);

    $data = [];
    $assessment = Assessment::with('assessmentMethodSetting','topicSetting','student','subjectUserThrough')->whereIn('student_id', [2])->orderByDesc('grading')->get();
    $assessmentMethod = AssessmentMethodSetting::all();
    $masterAssessmentMethod = [];
    foreach ($assessmentMethod as $key => $value) {
        $masterAssessmentMethod[$value->id] = [
                'assessment_method_id' => $value->id,
                'assessment_method_name' => $value->assessment_method_setting_name,
                'grade' => null,
        ];
    }

    foreach ($assessment as $key => $value) {
        if(!Helper::searchValueOnKey($data, 'student_id',$value->student_id)){
            $data[$value->student_id] = [
                'student_id' => $value->student_id,
                'student_name' => $value->student->student_name,
                'student_nis' => $value->student->student_nis,
                'student_nisn' => $value->student->student_nisn,
                'classroom' => $value->student->active_classroom_name,
                'term' => 'Genap',
                'schoolYear' => '2023-2024',
                'subjects' => []
                ];


            foreach ($assessment as $subject_key => $subject_value) {
                if(!Helper::searchValueOnKey($data[$value->student_id]['subjects'],'subject_id',$subject_value->subjectUserThrough->id)){
                    $data[$value->student_id]['subjects'][$subject_value->subjectUserThrough->id] = [
                            'subject_id' => $subject_value->subjectUserThrough->id,
                            'subject_name' => $subject_value->subjectUserThrough->subject_name,
                            'topics' => []
                    ];

                    foreach ($assessment as $topic_key => $topic_value) {
                        if(!Helper::searchValueOnKey($data[$value->student_id]['subjects'][$subject_value->subjectUserThrough->id],'topic_id',$topic_value->topic_setting_id)){
                            $data[$value->student_id]['subjects'][$subject_value->subjectUserThrough->id]['topics'][$topic_value->topic_setting_id] = [
                                'topic_id' => $topic_value->topic_setting_id,
                                'topic_setting_name' => $topic_value->topicSetting->topic_setting_name,
                                'assessment_method' => $masterAssessmentMethod,
                                'avg' => null
                            ];
                        }
                    }
                }
            }
        }
    }
    // dd($data);
    return view('print',['data'=>$data]);
 
	// $pdf = Pdf::loadview('print',['student'=>$student])->setPaper('a4', 'landscape')->setWarnings(false);
	// return $pdf->download('invoice.pdf');
})->name('students.print');