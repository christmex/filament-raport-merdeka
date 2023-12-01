<?php

namespace App\Imports;

use App\Helpers\Helper;
use App\Models\Religion;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImportAfterExport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $student = null;
        if($row['id'] != null){

            $religion = Religion::all();
            $student = Student::find($row['id']);
            $student->student_name = $row['student_name'];
            // $student->student_name = $row['student_name'] ? $row['student_name'] : $student->student_name;
            $student->student_nis = $row['student_nis'];
            // $student->student_nis = $row['student_nis'] ? $row['student_nis'] : $student->student_nis;
            $student->student_nisn = $row['student_nisn'];
            // $student->student_nisn = $row['student_nisn'] ? $row['student_nisn'] : $student->student_nisn;

            $student->born_place = $row['born_place'];
            $student->born_date = !empty($row['born_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['born_date']))) : NULL;
            $student->sex = Helper::getSexByName($row['sex']);
            $student->religion_id = $religion->where('name',$row['religion_id'])->count() ? $religion->where('name',$row['religion_id'])->first()->id : $student->religion_id;
            $student->status_in_family = $row['status_in_family'];
            $student->sibling_order_in_family = $row['sibling_order_in_family'];
            $student->address = $row['address'];
            $student->phone = $row['phone'];
            $student->previous_education = $row['previous_education'];
            $student->father_name = $row['father_name'];
            $student->mother_name = $row['mother_name'];
            $student->parent_address = $row['parent_address'];
            $student->parent_phone = $row['parent_phone'];
            $student->father_job = $row['father_job'];
            $student->mother_job = $row['mother_job'];
            $student->guardian_name = $row['guardian_name'];
            $student->guardian_phone = $row['guardian_phone'];
            $student->guardian_address = $row['guardian_address'];
            $student->guardian_job = $row['guardian_job'];
            $student->save();
        }
        return $student;
    }
}
