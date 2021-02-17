<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function pages(){
        return $this->hasMany(CollectionsPage::class);
    }
}
