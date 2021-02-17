<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Page extends Model
{

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function sharesGroup(){
        return $this->hasMany(ShareGroup::class);
    }

    public function blocs(){
        return $this->hasMany(Bloc::class);
    }


}
