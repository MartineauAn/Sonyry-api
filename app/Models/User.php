<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use App\Models\Page;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function inboxes()
    {
        return $this->hasMany(Inbox::class);
    }

    public function groupsMember(){
        return $this->hasMany(UserGroup::class);
    }

    public function sharesGroups(){
        return $this->hasMany(ShareGroup::class);
    }


    public function shareDirectories(){
        return $this->hasMany(ShareDirectory::class);
    }

    public function friends()
    {
        return $this->hasMany(Friend::class);
    }

    public function roles()
    {
        return $this->hasMany(RoleUser::class,'user_id');
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);

    }

}
