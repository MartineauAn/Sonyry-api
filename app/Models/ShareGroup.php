<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ShareGroup extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function page(){
        return $this->belongsTo(Page::class);
    }


    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function directories(){
        return $this->hasMany(ShareDirectory::class);
    }

    public function deleteShares($shares, $group){

        foreach ($shares as $share) {

            if ($share->group_id == $group->id) {

                $share->delete();
            }
        }
    }

    public static function isSharing($page){
        $shareGroups = self::where('page_id',$page->id)->get();

        if (count($shareGroups) > 0){
            foreach ($shareGroups as $shareGroup){
                $testUserGroup = UserGroup::where('user_id',Auth::user()->id)->where('group_id',$shareGroup->group_id);
                if ($testUserGroup !== null){
                    return true;
                }
            }
        }
        return false;
    }
}
