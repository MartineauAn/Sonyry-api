<?php

namespace App\Models;;

use Illuminate\Database\Eloquent\Model;

class ShareDirectory extends Model
{

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function parentDirectory(){
        return $this->belongsTo(ShareDirectory::class);
    }

    public function haveDirectory($user, $group){
        if (count(ShareDirectory::where('user_id', $user)->where('group_id', $group)->get()) > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function deleteDirectory($directory){
        if (count($directory) > 0) {
            $directory[0]->delete();
        }
    }
}
