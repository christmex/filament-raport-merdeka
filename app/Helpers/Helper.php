<?php

namespace App\Helpers;

use App\Models\SchoolSetting;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;

class Helper {

    public static $superUserEmail = 'super@sekolahbasic.sch.id';

    public static function getArrayDifKey($array1, $array2) :array{
        $keys1 = array_keys($array1);
        $keys2 = array_keys($array2);

        $diff1 = array_diff($keys1, $keys2);
        $diff2 = array_diff($keys2, $keys1);

        return array_merge($diff1, $diff2);
    }

    public static function getIndexPosition($array, $keyToFind)
    {
        $keys = array_keys($array);
        $position = array_search($keyToFind, $keys);

        return $position !== false ? $position : null;
    }

    public static function getFirstLetterFromWord($words){
        $words = preg_split("/\s+/", $words);
        $acronym = "";
        foreach ($words as $w) {
            $acronym .= mb_substr($w, 0, 1);
        }
        return $acronym;

    }

    public static function generateCharacterAvg($data){
        $avgTotal = [];
        $habitsTotal = 0;

        foreach($data as $AspectValue){
            foreach($AspectValue as $habitValue){
                $avgHome = [];
                $avgSchool = [];
                $countAvgHome = 0;
                $countAvgSchool = 0;
                $countAllAvg = 0;
                $habitsTotal++;

                $countI = 0;
                $countHabitValue = count($habitValue);
                
                foreach($habitValue as $weekVal){
                    foreach($weekVal as $homeOrSchoolKey => $homeOrSchool){
                        if($homeOrSchoolKey == 'home'){
                            array_push($avgHome, $homeOrSchool); 
                        }
                        if($homeOrSchoolKey == 'school'){
                            array_push($avgSchool, $homeOrSchool); 
                        }
                    }
                    if(++$countI === $countHabitValue){
                        $countHomeArray = array_filter($avgHome, function ($value) {
                            return $value !== null;
                        });
                        $countSchoolArray = array_filter($avgSchool, function ($value) {
                            return $value !== null;
                        });
                        if(count($countHomeArray) && count($countSchoolArray)){
                            $countAvgHome = round((array_sum($countHomeArray)/count($countHomeArray))/4, 1);
                            $countAvgSchool = round((array_sum($countSchoolArray)/count($countSchoolArray)), 1);
                            $countAllAvg = round(($countAvgHome*20/100)+($countAvgSchool*80/100),1);
                            array_push($avgTotal, $countAllAvg);
                        }
                    }
                }
            }

        }

        return round(round(array_sum($avgTotal),1) / $habitsTotal,1);


    }

    public static function getOnlyFinalAvgAcademic($data,$avgDiv, $PASDiv){
        $result = [];
        foreach($data as $student_name => $subjects ){
            $avg = [];
            foreach($subjects as $subjectKey => $subjectValue ){
                array_push($avg, self::countFinalGrade($subjectValue['AVG'],$subjectValue['PAS'],$avgDiv, $PASDiv));
            }
            $result[$student_name] = round(array_sum($avg) / count($avg),1)/10;
        }

        return $result;
    }

    public static function generateRank($data,$getOnlyFinalAvgAcademic){
        $rank = [];
        $character = [];
        foreach ($data as $key => $value) {
            $character[$key] = self::generateCharacterAvg($value);
        }

        if(count($character) == count($getOnlyFinalAvgAcademic)){
            foreach ($character as $key => $value) {
                $rank[$key] = round(($getOnlyFinalAvgAcademic[$key]*75/100+$character[$key]*25/100*2.5),1);
            }
        }
        
        arsort($rank);
        $start = 1;
        foreach ($rank as $key => $value) {
            $rank[$key] = $start;
            $start++;
        }
        return $rank;
    }

    public static function getDefaultMetaForSchoolSetting(){
        return [
            "show_fase" => "1",
            "show_top_kkm" => "0"
        ];
    }

    public static function predicate($avg, $kkm, $inEnglish = false, $type = null){

        if($type == null){
            $A = 100-(100 - $kkm)/3 ;
            $B = $A-(100-$kkm)/3 ;
            $C = $B-(100-$kkm)/3-0.5;

            if ($avg > $A) {
                $result = $inEnglish ? 'Showing the abilty to understand the concept of' :"Menunjukkan kemampuan memahami konsep dalam";
            } elseif ($avg > $B) {
                $result = $inEnglish ? 'Showing the abilty to understand the concept of' :"Menunjukkan kemampuan memahami konsep dalam";
            } elseif ($avg > $C) {
                $result = $inEnglish ? 'Need to improve about ' :"Perlu bimbingan dalam";
            } else {
                $result = $inEnglish ? 'Need to improve about ' :"Perlu bimbingan dalam";
            }
        }else {
            $result = self::staticPredicate($inEnglish, $type);
        }
        

        return $result;
    }

    public static function staticPredicate($inEnglish, $type){
        if($type == 'past'){
            $result = $inEnglish ? 'Showing the abilty to understand the concept of' :"Menunjukkan kemampuan memahami konsep dalam";
        }elseif($type == 'under') {
            $result = $inEnglish ? 'need to improve about ' :"perlu bimbingan dalam";
        }

        return $result;
    }

    public static function predicateInEnglish($avg, $kkm){

    }

    public static function getSex($id){
        return $id == 0 ? "Perempuan" : "Laki-laki";
    }
    
    public static function getSexByName($name){
        if($name == "Perempuan"){
            return 0;
        }elseif($name == "Laki-laki"){
            return 1;
        }else {
            return null;
        }
        // return $name == "Perempuan" ? 0  : 1;
    }

    public static function findValueByNestedKey($array, $searchKey) {
        foreach ($array as $key => $value) {
            if ($key === $searchKey) {
                return $value;
            }
    
            if (is_array($value)) {
                $result = self::findValueByNestedKey($value, $searchKey);
                if ($result !== null) {
                    return $result;
                }
            }
        }
    
        return null; // Key not found in the nested array
    }

    public static function getKeyByValue($array, $value) {
        foreach ($array as $key => $element) {
            if ($element === $value) {
                return $key;
            }
        }
        return null; // Return null if the value is not found in the array
    }
    

    public static function calculateAverage($data) {
        $averages = [];

        // Iterasi melalui semua mata pelajaran (misalnya, 'Maths', 'Science', dll.)
        foreach ($data as $subject => $topics) {
            $totalSum = 0;
            $totalCount = 0;

            // Iterasi melalui semua topik dalam mata pelajaran
            foreach ($topics as $topic => $subTopics) {
                // Tambahkan validasi untuk melewatkan jika topik adalah 'KKM'
                if ($topic === 'KKM') {
                    $averages[$subject]['KKM'] = $subTopics;
                    continue;
                }
                if($topic === 'subject_user_id'){
                    // $averages[$subject]['subject_user_id'] = $subTopics;
                    continue;
                }
                if($topic === 'is_curiculum_basic'){
                    continue;
                }


                // Iterasi melalui semua subtopik dalam topik
                foreach ($subTopics as $subTopic => $values) {
                    if (isset($values['grading'])) {
                        // Jika 'grading' ada, tambahkan ke totalSum
                        $totalSum += (float)$values['grading'];
                        $totalCount++;
                    }
                }
            }
    
            // Menghindari pembagian oleh nol
            if ($totalCount === 0) {
                $average = 0;
            } else {
                $average = round($totalSum / $totalCount); // Memasukkan fungsi round di sini
            }
    
            // Simpan rata-rata dalam array dengan nama mata pelajaran sebagai kunci
            $averages[$subject] = ['AVG'=> $average];
        }
    
        return $averages;
    }

    public static function calculateAvgTopic($data){
        $averages = [];

        foreach ($data as $subject => $topics) {
            $subjectAverages = [];
            $kkm = 0;
            $subject_user_id = 0;
            
            foreach ($topics as $topic => $subTopics) {
                if ($topic === 'KKM') {
                    $kkm = is_array($subTopics) ? (float)$subTopics['grading'] : (float)$subTopics;
                    continue;
                }
                if($topic === 'subject_user_id'){
                    continue;
                }

                if($topic === 'is_curiculum_basic'){
                    continue;
                }

                $totalSum = 0;
                $totalCount = 0;

                foreach ($subTopics as $subTopic => $values) {
                    if (isset($values['grading'])) {
                        $totalSum += (float)$values['grading'];
                        $totalCount++;
                    }
                }

                if ($totalCount > 0) {
                    $topicAverage = round($totalSum / $totalCount);
                    $subjectAverages[$topic] = $topicAverage;
                }
            }

            if (!empty($subjectAverages) && $kkm > 0) {
                $averages[$subject] = $subjectAverages;
            }
        }

        return $averages;
    }
    
    public static function searchValueOnKey($array, $keyToSearch, $valueToFind) {
        foreach ($array as $subArray) {
            if (is_array($subArray)) {
                if (self::searchValueOnKey($subArray, $keyToSearch, $valueToFind)) {
                    return true; // Value found on the specified key in a sub-array
                }
            } elseif (isset($array[$keyToSearch]) && $array[$keyToSearch] === $valueToFind) {
                return true; // Value found on the specified key in the current level
            }
        }
        return false; // Key not found in the array
    }

    public static function findValueByKey($array, $key) {
        foreach ($array as $item) {
            if (is_array($item)) {
                if (self::findValueByKey($item, $key)) {
                    return true;
                }
            } else {
                if ($item === $key) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function findSubjectByName($array, $subjectName) {
        foreach ($array as $key => $item) {
            if (isset($item['subject_name']) && $item['subject_name'] === $subjectName) {
                return [$key,$item];
            }
        }
        return null; // Return null if the subject is not found
    }

    public static function CheckAssessment($object, $array, $key, $topicSettingId, $assessmentMethodId){
        if(!self::findValueByKey($array, $key)){
            if($object->topic_setting_id == $topicSettingId && $object->assessment_method_setting_id == $assessmentMethodId){
                // return $object->grading;
                return $object->max_grading;
            }
        }
        return null;
    }

    public static function topicAvg($array){
        $filteredArray = array_filter($array,function($var){
            return ($var !== NULL && $var !== FALSE && $var !== '');
        });

        if (count($filteredArray) > 0) {
            // Calculate the average
            $average = array_sum($filteredArray) / count($filteredArray);
            return $average;
        }
    }

    public static function setActiveSchoolYear(): void
    {
        request()->session()->put('active_school_year_id',SchoolYear::activeId());
    }
    public static function getActiveSchoolYear(){
        // check if the session already provide, if dont create one
        if(!session('active_school_year_id')){
            self::setActiveSchoolYear();
        }

        return session('active_school_year_id');
    }

    public static function isSchoolYearActive(): bool
    {
        if(self::getActiveSchoolYear()){
            return true;
        }else {
            return false;
        }
    }
    
    public static function setActiveSchoolTerm(): void
    {
        request()->session()->put('active_school_term_id',SchoolTerm::activeId());
    }
    public static function getActiveSchoolTerm(){
        // check if the session already provide, if dont create one
        if(!session('active_school_term_id')){
            self::setActiveSchoolTerm();
        }

        return session('active_school_term_id');
    }

    public static function isSchoolTermActive(): bool
    {
        if(self::getActiveSchoolTerm()){
            return true;
        }else {
            return false;
        }
    }

    public static function getSchoolYearName(): ?string
    {
        return SchoolYear::where('school_year_status', true)->first()->school_year_name;
    }

    public static function getSchoolTermName(): ?string
    {
        return SchoolTerm::where('school_term_status', true)->first()->school_term_name;
    }

    public static function getSchoolSetting()
    {
        return SchoolSetting::first();
    }

    public static function generateRaportData($object){
        $dataList = [];
        foreach ($object as $key => $value) {
            // Cek apakah subject ini sudah ada di data array?
            if(self::searchValueOnKey($dataList, 'subject_name',$value->subjectUserThrough->subject_name)){
    
                // Yes
                $initData = self::findSubjectByName($dataList,$value->subjectUserThrough->subject_name);
                if(!($initData[1]['model_value']['assessment_method_setting_id'] == $value->assessment_method_setting_id && $initData[1]['model_value']['topic_setting_id'] == $value->topic_setting_id)){
                    // jika beda, maka kita harus tambahkan ke key yang bersangkutan, 
    
                    $dataList[$initData[0]]['topic_1_tes_lisan'] = $dataList[$initData[0]]['topic_1_tes_lisan'] ?? self::CheckAssessment($value,$dataList[$initData[0]], 'topic_1_tes_lisan', 1, 1);
                    $dataList[$initData[0]]['topic_1_penugasan'] = $dataList[$initData[0]]['topic_1_penugasan'] ??  self::CheckAssessment($value,$dataList[$initData[0]], 'topic_1_penugasan', 1, 2) ;
                    $dataList[$initData[0]]['topic_1_kinerja'] = $dataList[$initData[0]]['topic_1_kinerja'] ??  self::CheckAssessment($value,$dataList[$initData[0]], 'topic_1_kinerja', 1, 3) ;
                    $dataList[$initData[0]]['topic_1_monthly_test'] = $dataList[$initData[0]]['topic_1_monthly_test'] ??  self::CheckAssessment($value,$dataList[$initData[0]], 'topic_1_monthly_test', 1, 4) ;
                    $dataList[$initData[0]]['topic_1_avg'] =  self::topicAvg([
                        $dataList[$initData[0]]['topic_1_tes_lisan'],
                        $dataList[$initData[0]]['topic_1_penugasan'],
                        $dataList[$initData[0]]['topic_1_kinerja'],
                        $dataList[$initData[0]]['topic_1_monthly_test']
                    ]);
    
                   $dataList[$initData[0]]['topic_2_tes_lisan'] = $dataList[$initData[0]]['topic_2_tes_lisan'] ?? self::CheckAssessment($value,$dataList[$initData[0]], 'topic_2_tes_lisan', 2, 1) ;
                   $dataList[$initData[0]]['topic_2_penugasan'] = $dataList[$initData[0]]['topic_2_penugasan'] ?? self::CheckAssessment($value,$dataList[$initData[0]], 'topic_2_penugasan', 2, 2) ;
                   $dataList[$initData[0]]['topic_2_kinerja'] = $dataList[$initData[0]]['topic_2_kinerja'] ?? self::CheckAssessment($value,$dataList[$initData[0]], 'topic_2_kinerja', 2, 3) ;
                   $dataList[$initData[0]]['topic_2_monthly_test'] = $dataList[$initData[0]]['topic_2_monthly_test'] ?? self::CheckAssessment($value,$dataList[$initData[0]], 'topic_2_monthly_test', 2, 4) ;
                   $dataList[$initData[0]]['topic_2_avg'] = self::topicAvg([
                        $dataList[$initData[0]]['topic_2_tes_lisan'],
                        $dataList[$initData[0]]['topic_2_penugasan'],
                        $dataList[$initData[0]]['topic_2_kinerja'],
                        $dataList[$initData[0]]['topic_2_monthly_test']
                    ]);
    
                    $dataList[$initData[0]]['topic_3_tes_lisan'] = $dataList[$initData[0]]['topic_3_tes_lisan'] ?? self::CheckAssessment($value,$dataList[$initData[0]], 'topic_3_tes_lisan', 3, 1) ;
                    $dataList[$initData[0]]['topic_3_penugasan'] = $dataList[$initData[0]]['topic_3_penugasan'] ?? self::CheckAssessment($value,$dataList[$initData[0]], 'topic_3_penugasan', 3, 2) ;
                    $dataList[$initData[0]]['topic_3_kinerja'] = $dataList[$initData[0]]['topic_3_kinerja'] ?? self::CheckAssessment($value,$dataList[$initData[0]], 'topic_3_kinerja', 3, 3) ;
                    $dataList[$initData[0]]['topic_3_monthly_test'] = $dataList[$initData[0]]['topic_3_monthly_test'] ?? self::CheckAssessment($value,$dataList[$initData[0]], 'topic_3_monthly_test', 3, 4) ;
                    $dataList[$initData[0]]['topic_3_avg'] = self::topicAvg([
                        $dataList[$initData[0]]['topic_3_tes_lisan'],
                        $dataList[$initData[0]]['topic_3_penugasan'],
                        $dataList[$initData[0]]['topic_3_kinerja'],
                        $dataList[$initData[0]]['topic_3_monthly_test']
                    ]);
    
                    $dataList[$initData[0]]['model_value'] = $value->toArray();
    
                    
                }
    
            }else {
                // No,init the data
                $dataList[] = [
                    'subject_name' => $value->subjectUserThrough->subject_name,
                    'topic_1_tes_lisan' => self::CheckAssessment($value,$dataList, 'topic_1_tes_lisan', 1, 1),
                    'topic_1_penugasan' => self::CheckAssessment($value,$dataList, 'topic_1_penugasan', 1, 2),
                    'topic_1_kinerja' => self::CheckAssessment($value,$dataList, 'topic_1_kinerja', 1, 3),
                    'topic_1_monthly_test' => self::CheckAssessment($value,$dataList, 'topic_1_monthly_test', 1, 4),
                    'topic_1_avg' => self::topicAvg([
                        self::CheckAssessment($value,$dataList, 'topic_1_tes_lisan', 1, 1),
                        self::CheckAssessment($value,$dataList, 'topic_1_penugasan', 1, 2),
                        self::CheckAssessment($value,$dataList, 'topic_1_kinerja', 1, 3),
                        self::CheckAssessment($value,$dataList, 'topic_1_monthly_test', 1, 4),
                    ]),
    
    
                    'topic_2_tes_lisan' => self::CheckAssessment($value,$dataList, 'topic_2_tes_lisan', 2, 1),
                    'topic_2_penugasan' => self::CheckAssessment($value,$dataList, 'topic_2_penugasan', 2, 2),
                    'topic_2_kinerja' => self::CheckAssessment($value,$dataList, 'topic_2_kinerja', 2, 3),
                    'topic_2_monthly_test' => self::CheckAssessment($value,$dataList, 'topic_2_monthly_test', 2, 4),
                    'topic_2_avg' => self::topicAvg([
                        self::CheckAssessment($value,$dataList, 'topic_2_tes_lisan', 2, 1),
                        self::CheckAssessment($value,$dataList, 'topic_2_penugasan', 2, 2),
                        self::CheckAssessment($value,$dataList, 'topic_2_kinerja', 2, 3),
                        self::CheckAssessment($value,$dataList, 'topic_2_monthly_test', 2, 4),
                    ]),
    
    
                    'topic_3_tes_lisan' => self::CheckAssessment($value,$dataList, 'topic_3_tes_lisan', 3, 1),
                    'topic_3_penugasan' => self::CheckAssessment($value,$dataList, 'topic_3_penugasan', 3, 2),
                    'topic_3_kinerja' => self::CheckAssessment($value,$dataList, 'topic_3_kinerja', 3, 3),
                    'topic_3_monthly_test' => self::CheckAssessment($value,$dataList, 'topic_3_monthly_test', 3, 4),
                    'topic_3_avg' => self::topicAvg([
                        self::CheckAssessment($value,$dataList, 'topic_3_tes_lisan', 3, 1),
                        self::CheckAssessment($value,$dataList, 'topic_3_penugasan', 3, 2),
                        self::CheckAssessment($value,$dataList, 'topic_3_kinerja', 3, 3),
                        self::CheckAssessment($value,$dataList, 'topic_3_monthly_test', 3, 4),
                    ]),
    
                    'model_value' => $value->toArray()
                ];
            }
        }

        return $dataList;
    }

    public static function numberToRomawi($number){
        switch ($number) {
            case 1:
                echo "I";
                break;
            case 2:
                echo "II";
                break;
            case 3:
                echo "III";
                break;
            case 4:
                echo "IV";
                break;
            case 5:
                echo "V";
                break;
            case 6:
                echo "VI";
                break;
            case 7:
                echo "VII";
                break;
            case 8:
                echo "VIII";
                break;
            case 9:
                echo "IX";
                break;
            case 10:
                echo "X";
                break;
            case 11:
                echo "XI";
                break;
            case 12:
                echo "XII";
                break;
            
            default:
                echo "-";
                break;
        }
    }

    public static function convertTopicIndoVersion($teks){
        switch ($teks) {
            case 'Topic 1':
                echo "Topik 1";
                break;
            case 'Topic 2':
                echo "Topik 2";
                break;
            case 'Topic 3':
                echo "Topik 3";
                break;
            
            default:
                echo $teks;
                break;
        }
    }

    public static function countFinalGrade($avg, $pas, $avgDiv, $pasDiv){

        if(empty($avgDiv) || empty($pasDiv)){
            return "Kontak adm untuk setting alokasi bobot di menu school setting";
        }
        
        $result = $avg*$avgDiv;
        if($pas){
            $result +=$pas*$pasDiv;
        }

        return round($result);
    }

    public static function reportSheetCalculateAverage($datas){
        $averages = [];

        foreach($datas as $keyData => $data){
            foreach ($data as $subject => $topics) {
                $totalSum = 0;
                $totalCount = 0;
    
                // Iterasi melalui semua topik dalam mata pelajaran
                foreach ($topics as $topic => $subTopics) {
                    // Tambahkan validasi untuk melewatkan jika topik adalah 'KKM'
                    if ($topic === 'KKM') {
                        $averages[$keyData][$subject]['KKM'] = $subTopics;
                        continue;
                    }
                    // if($topic === 'subject_user_id'){
                    //     continue;
                    // }
                    if($topic === 'is_curiculum_basic'){
                        continue;
                    }
                    // Iterasi melalui semua subtopik dalam topik
                    foreach ($subTopics as $subTopic => $values) {
                        if (isset($values['grading'])) {
                            // Jika 'grading' ada, tambahkan ke totalSum
                            $totalSum += (float)$values['grading'];
                            $totalCount++;
                        }
                    }
                }
        
                // Menghindari pembagian oleh nol
                if ($totalCount === 0) {
                    $average = 0;
                } else {
                    $average = round($totalSum / $totalCount); // Memasukkan fungsi round di sini
                }
        
                // Simpan rata-rata dalam array dengan nama mata pelajaran sebagai kunci
                $averages[$keyData][$subject] = ['AVG'=> $average];
            }
        }
        // Iterasi melalui semua mata pelajaran (misalnya, 'Maths', 'Science', dll.)
    
        return $averages;
    }


}
