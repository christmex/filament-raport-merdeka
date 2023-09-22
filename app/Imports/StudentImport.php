<?php

namespace App\Imports;

use App\Models\Student;
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
        $student = Student::where('student_nis',$row['student_nis'])->orWhere('student_nisn', $row['student_nisn'])->first();
        if(!$student){
            return new Student([
                'student_name' => $row['student_name'],
                'student_nis' => $row['student_nis'],
                'student_nisn' => $row['student_nisn'],
            ]);
        }
    }
}
