<?php

namespace App\Imports;

use App\Models\Assessment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssessmentImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row['id']){
            $Assessment = Assessment::find($row['id']);
            $Assessment->update(['grading' => $row['grading']]);
            $Assessment->save();
            return $Assessment;
        }
        // else nmya buat inser data baru
        return null;
            
    }
}
