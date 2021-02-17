<?php

namespace App\Policies;


use App\Models\Page;
use App\Models\RoleUserPolicy;
use App\Models\ShareGroup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class PagePolicy
{
    use HandlesAuthorization;

    /* //Permet d'exécuter avant de vérifier. Peut être utilisé pour le partage des pages/collection groupes.
    public function before(User $user, $ability){
        if($user->AsRight()){
            return true;
        }
    }*/

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

    public function access(User $user, Page $page){

        return $user->id == $page->user_id;

    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Page $page
     * @return mixed
     */
    public function view(User $user, Page $page)
    {
        $rolePolicies = new RoleUserPolicy();

        if ($user->id == $page->user_id) {
            return true;
        }

        if ($rolePolicies->role($rolePolicies->getTeacher())) {
            return true;
        }

        if ($rolePolicies->role($rolePolicies->getJury())) {
            return true;
        }

        return ShareGroup::isSharing($page);
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
     * @param Page $page
     * @return mixed
     */
    public function update(User $user, Page $page)
    {
        return $user->id == $page->user_id;

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Page $page
     * @return mixed
     */
    public function delete(User $user, Page $page)
    {
        return $user->id == $page->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Page $page
     * @return mixed
     */
    public function restore(User $user, Page $page)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Page $page
     * @return mixed
     */
    public function forceDelete(User $user, Page $page)
    {
        //
    }

    public function createBloc(User $user, Page $page)
    {
        return $user->id == $page->user_id;
    }
}
