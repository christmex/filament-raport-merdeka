<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\StudentClassroom;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        if($row['student_nis'] != null || $row['student_nisn'] != null){
            $student = Student::query()
                    ->where('student_nis',$row['student_nis'])
                    ->orWhere('student_nisn', $row['student_nisn'])
                    ->first();
            if(!$student){
                $student = Student::create([
                    'student_name' => $row['student_name'],
                    'student_nis' => $row['student_nis'],
                    'student_nisn' => $row['student_nisn'],
                ]);
            
                $getUserHomerooms = auth()->user()->activeHomeroom->first();
                if($getUserHomerooms){
                    StudentClassroom::create([
                        'student_id' => $student->id,
                        'homeroom_teacher_id' => $getUserHomerooms->id,
                    ]);
                }
        
                return $student;
            }
        }else {
            $student = Student::create([
                'student_name' => $row['student_name'],
                'student_nis' => $row['student_nis'],
                'student_nisn' => $row['student_nisn'],
            ]);
        
            $getUserHomerooms = auth()->user()->activeHomeroom->first();
            if($getUserHomerooms){
                StudentClassroom::create([
                    'student_id' => $student->id,
                    'homeroom_teacher_id' => $getUserHomerooms->id,
                ]);
            }
    
            return $student;
        }
        
    }
}
