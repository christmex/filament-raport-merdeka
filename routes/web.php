<?php

use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\TopicSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\AssessmentMethodSetting;
use App\Models\SchoolYear;

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
    // session(['active_school_year_id' => 1]);
    dd(session('active_school_year_id'),session('active_school_term_id'));
});
Route::get('/print/{student}', function(Student $student){
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
            '2' => 60,

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


    $kk = [];


    $assessments = Assessment::with('assessmentMethodSetting','topicSetting','student','subjectUserThrough')->where('student_id', $student->id)->orderByDesc('grading')->withoutGlobalScope('subjectUser')->get();
    
    
    foreach ($assessments as $key => $value) {
        if(!Helper::searchValueOnKey($kk, 'subject_name',$value->subjectUserThrough->subject_name)){
            $kk[] = [
                'subject_name' => $value->subjectUserThrough->subject_name,
                'topic_1_tes_lisan' => Helper::CheckAssessment($value,$kk, 'topic_1_tes_lisan', 1, 1),
                'topic_1_penugasan' => Helper::CheckAssessment($value,$kk, 'topic_1_penugasan', 1, 2),
                'topic_1_kinerja' => Helper::CheckAssessment($value,$kk, 'topic_1_kinerja', 1, 3),
                'topic_1_monthly_test' => Helper::CheckAssessment($value,$kk, 'topic_1_monthly_test', 1, 4),
                'topic_1_avg' => Helper::topicAvg([
                    Helper::CheckAssessment($value,$kk, 'topic_1_tes_lisan', 1, 1),
                    Helper::CheckAssessment($value,$kk, 'topic_1_penugasan', 1, 2),
                    Helper::CheckAssessment($value,$kk, 'topic_1_kinerja', 1, 3),
                    Helper::CheckAssessment($value,$kk, 'topic_1_monthly_test', 1, 4),
                ]),

                'topic_2_tes_lisan' => Helper::CheckAssessment($value,$kk, 'topic_2_tes_lisan', 2, 1),
                'topic_2_penugasan' => Helper::CheckAssessment($value,$kk, 'topic_2_penugasan', 2, 2),
                'topic_2_kinerja' => Helper::CheckAssessment($value,$kk, 'topic_2_kinerja', 2, 3),
                'topic_2_monthly_test' => Helper::CheckAssessment($value,$kk, 'topic_2_monthly_test', 2, 4),
                'topic_2_avg' => Helper::topicAvg([
                    Helper::CheckAssessment($value,$kk, 'topic_2_tes_lisan', 2, 1),
                    Helper::CheckAssessment($value,$kk, 'topic_2_penugasan', 2, 2),
                    Helper::CheckAssessment($value,$kk, 'topic_2_kinerja', 2, 3),
                    Helper::CheckAssessment($value,$kk, 'topic_2_monthly_test', 2, 4),
                ]),

                'topic_3_tes_lisan' => Helper::CheckAssessment($value,$kk, 'topic_3_tes_lisan', 3, 1),
                'topic_3_penugasan' => Helper::CheckAssessment($value,$kk, 'topic_3_penugasan', 3, 2),
                'topic_3_kinerja' => Helper::CheckAssessment($value,$kk, 'topic_3_kinerja', 3, 3),
                'topic_3_monthly_test' => Helper::CheckAssessment($value,$kk, 'topic_3_monthly_test', 3, 4),
                'topic_3_avg' => Helper::topicAvg([
                    Helper::CheckAssessment($value,$kk, 'topic_3_tes_lisan', 3, 1),
                    Helper::CheckAssessment($value,$kk, 'topic_3_penugasan', 3, 2),
                    Helper::CheckAssessment($value,$kk, 'topic_3_kinerja', 3, 3),
                    Helper::CheckAssessment($value,$kk, 'topic_3_monthly_test', 3, 4),
                ]),
            ];
        }
    }

    // return view('print',['data'=>$kk, 'student',$student]);
    return view('print',compact('kk','student'));

    // dd($kk, $assessments);







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