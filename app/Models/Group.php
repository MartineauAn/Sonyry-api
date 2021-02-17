<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function shares(){
        return $this->hasMany(ShareGroup::class);
    }

    public function directories(){
        return $this->hasMany(ShareDirectory::class);
    }

    public function members(){
        return $this->hasMany(UserGroup::class);
    }
}
