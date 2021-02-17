<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionsPage extends Model
{
    public function page(){
        return $this->belongsTo(Page::class);
    }

    public function collection(){
        return $this->belongsTo(Collection::class);
    }


}
