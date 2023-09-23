<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\AssessmentMethodSetting;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssessmentMethodSettingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
        // if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
        //     return $user->email == "super@sekolahbasic.sch.id";
        // }
        // return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AssessmentMethodSetting $assessmentMethodSetting): bool
    {
        return false;
        // if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
        //     return $user->email == "super@sekolahbasic.sch.id";
        // }
        // return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
        // if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
        //     return $user->email == "super@sekolahbasic.sch.id";
        // }
        // return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AssessmentMethodSetting $assessmentMethodSetting): bool
    {
        return false;
        // if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
        //     return $user->email == "super@sekolahbasic.sch.id";
        // }
        // return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssessmentMethodSetting $assessmentMethodSetting): bool
    {
        return false;
        // if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
        //     return $user->email == "super@sekolahbasic.sch.id";
        // }
        // return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AssessmentMethodSetting $assessmentMethodSetting): bool
    {
        return false;
        // if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
        //     return $user->email == "super@sekolahbasic.sch.id";
        // }
        // return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AssessmentMethodSetting $assessmentMethodSetting): bool
    {
        return false;
        // if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
        //     return $user->email == "super@sekolahbasic.sch.id";
        // }
        // return false;
    }
}
