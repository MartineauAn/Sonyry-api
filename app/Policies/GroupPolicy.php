<?php

namespace App\Policies;


use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Group $group
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        if($user->id == $group->user_id){
            return true;
        }
        else{
            foreach ($group->members as $member) {
                if ($member->user_id == $user->id){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Group $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        return $user->id == $group->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Group $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return $user->id == $group->user_id;
    }

    public function kick(User $user, Group $group)
    {
        return $user->id == $group->user_id;

    }

    public function exit(User $user, Group $group){
        return UserGroup::where('user_id',$user->id)->where('group_id',$group->id)->get() != null && $group->user_id != $user->id;
    }

    public function invite(User $user, Group $group)
    {
        return $group->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Group $group
     * @return mixed
     */
    public function restore(User $user, Group $group)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Group $group
     * @return mixed
     */
    public function forceDelete(User $user, Group $group)
    {
        //
    }
}
