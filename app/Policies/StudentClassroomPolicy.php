<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StudentClassroom;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentClassroomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $activeSubject = $user->activeSubjects->first(function ($activeSubject) {
            return $activeSubject->classroom->is_moving_class === true;
        });

        return $activeSubject != null || $user->can('view_any_student::classroom');
        // return $user->activeHomeroom->count() > 0;
        // return $user->can('view_any_student::classroom');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudentClassroom  $studentClassroom
     * @return bool
     */
    public function view(User $user, StudentClassroom $studentClassroom): bool
    {
        return $user->can('view_student::classroom');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_student::classroom');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudentClassroom  $studentClassroom
     * @return bool
     */
    public function update(User $user, StudentClassroom $studentClassroom): bool
    {
        return $user->can('update_student::classroom');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudentClassroom  $studentClassroom
     * @return bool
     */
    public function delete(User $user, StudentClassroom $studentClassroom): bool
    {
        return $user->can('delete_student::classroom');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_student::classroom');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudentClassroom  $studentClassroom
     * @return bool
     */
    public function forceDelete(User $user, StudentClassroom $studentClassroom): bool
    {
        return $user->can('force_delete_student::classroom');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_student::classroom');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudentClassroom  $studentClassroom
     * @return bool
     */
    public function restore(User $user, StudentClassroom $studentClassroom): bool
    {
        return $user->can('restore_student::classroom');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_student::classroom');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudentClassroom  $studentClassroom
     * @return bool
     */
    public function replicate(User $user, StudentClassroom $studentClassroom): bool
    {
        return $user->can('replicate_student::classroom');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_student::classroom');
    }

}
