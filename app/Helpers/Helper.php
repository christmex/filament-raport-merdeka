<?php

namespace App\Helpers;

use App\Models\SchoolSetting;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;

class Helper {

    public static $superUserEmail = 'super@sekolahbasic.sch.id';
    
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


}
