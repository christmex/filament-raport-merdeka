<?php

namespace App\Helpers;

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

    public static function CheckAssessment($object, $array, $key, $topicSettingId, $assessmentMethodId){
        if(!self::findValueByKey($array, $key)){
            if($object->topic_setting_id == $topicSettingId && $object->assessment_method_setting_id == $assessmentMethodId){
                return $object->grading;
            }
        }
        return null;
    }

    public static function topicAvg($array){
        // return count(array_filter($array));
        // $div = count(array_filter($array));
        // return array_sum(array_filter($array))/$div;

        // $array = [null, 1, null];

        // Filter out null values
        $filteredArray = array_filter($array);

        if (count($filteredArray) > 0) {
            // Calculate the average
            $average = array_sum($filteredArray) / count($filteredArray);
            return $average;
        }
    }
}
