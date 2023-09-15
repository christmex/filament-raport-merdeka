<?php

use App\Models\Assessment;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

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

Route::get('/print', function(){
    $data = [
        ''
    ];
    $student = Assessment::all();
    dd($student);
    // return view('print');
 
	// $pdf = Pdf::loadview('print',['student'=>$student])->setPaper('a4', 'landscape')->setWarnings(false);
	// return $pdf->download('invoice.pdf');
});