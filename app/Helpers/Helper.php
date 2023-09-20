<?php

namespace App\Helpers;

use App\Models\SchoolSetting;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;

class Helper {
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
        $filteredArray = array_filter($array);

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



}
