<?php

namespace App\Policies;


use App\Models\Categorie;
use App\Models\RoleUserPolicy;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @param Categorie $categorie
     * @return mixed
     */
    public function viewAny(User $user, Categorie $categorie)
    {
        $rolePolicy = new RoleUserPolicy();
        return $rolePolicy->role($rolePolicy->getTeacher()) || $rolePolicy->role($rolePolicy->getAdmin());
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Categorie $categorie
     * @return mixed
     */
    public function view(User $user, Categorie $categorie)
    {

    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Categorie $categorie
     * @return mixed
     */
    public function update(User $user, Categorie $categorie)
    {
        $rolePolicy = new RoleUserPolicy();
        return $rolePolicy->role($rolePolicy->getTeacher()) || $rolePolicy->role($rolePolicy->getAdmin());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Categorie $categorie
     * @return mixed
     */
    public function delete(User $user, Categorie $categorie)
    {
        $rolePolicy = new RoleUserPolicy();
        return $rolePolicy->role($rolePolicy->getTeacher()) || $rolePolicy->role($rolePolicy->getAdmin());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Categorie $categorie
     * @return mixed
     */
    public function restore(User $user, Categorie $categorie)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Categorie $categorie
     * @return mixed
     */
    public function forceDelete(User $user, Categorie $categorie)
    {
        //
    }
}
