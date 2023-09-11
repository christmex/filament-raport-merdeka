<?php

use App\Models\Student;
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
    // return view('print');
    // $student = Student::all();
 
	// $pdf = PDF::loadview('pegawai_pdf',['pegawai'=>$pegawai]);
	// return $pdf->download('laporan-pegawai-pdf');
});