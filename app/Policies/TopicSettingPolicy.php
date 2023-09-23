<?php

namespace App\Policies;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\TopicSetting;
use Illuminate\Auth\Access\Response;

class TopicSettingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // return false;
        if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
            return $user->email == "super@sekolahbasic.sch.id";
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TopicSetting $topicSetting): bool
    {
        // return false;
        if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
            return $user->email == "super@sekolahbasic.sch.id";
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // return false;
        if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
            return $user->email == "super@sekolahbasic.sch.id";
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TopicSetting $topicSetting): bool
    {
        // return false;
        if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
            return $user->email == "super@sekolahbasic.sch.id";
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TopicSetting $topicSetting): bool
    {
        // return false;
        if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
            return $user->email == "super@sekolahbasic.sch.id";
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TopicSetting $topicSetting): bool
    {
        // return false;
        if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
            return $user->email == "super@sekolahbasic.sch.id";
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TopicSetting $topicSetting): bool
    {
        // return false;
        if(Helper::isSchoolYearActive() && Helper::isSchoolTermActive()){
            return $user->email == "super@sekolahbasic.sch.id";
        }
        return false;
    }
}
