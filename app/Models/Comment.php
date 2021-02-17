<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function commentable()
    {
        return $this->morphTo();
    }


    public function comments()
    {
        return $this->morphMany(__CLASS__, 'commentable')->latest();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
