<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->activeHomeroom->count() > 0;
        // dd($user);
        // return $user->has('homerooms');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student): bool
    {
        return $user->activeHomeroom->count() > 0;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->activeHomeroom->count() > 0;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Student $student): bool
    {
        return $user->activeHomeroom->count() > 0;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        // return $user->activeHomeroom->count() > 0 && auth()->user()->email == 'super@sekolahbasic.sch.id';
        // return $user->activeHomeroom->count() > 0;
        return $user->can('delete_student');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteAny(User $user): bool
    {
        // return $user->activeHomeroom->count() > 0;
        return $user->can('delete_any_student');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Student $student): bool
    {
        // return $user->activeHomeroom->count() > 0 && auth()->user()->email == 'super@sekolahbasic.sch.id';
        return $user->activeHomeroom->count() > 0;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        // return $user->activeHomeroom->count() > 0 && auth()->user()->email == 'super@sekolahbasic.sch.id';
        return $user->activeHomeroom->count() > 0;
    }
}
