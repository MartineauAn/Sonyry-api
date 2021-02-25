<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function destroy($id)
    {
        Friend::find($id)->delete();
        return response()->json();

    }

    public function add($id)
    {

        $friend=Friend::find($id);

        if (count(Friend::where('target',$friend->sender)->where('sender',Auth::user()->id)->get()) === 0) {

            $friend->is_pending = 0;

            $friend->save();

            return response()->json();
        }

        $friend->delete();

        return response()->json(null,401);
    }

    public function request($id)
    {
        if (count(Friend::where('target',Auth::user()->id)->where('sender',$id)->get()) === 0){
            $friend= new Friend();
            $friend->sender=Auth::user()->id;
            $friend->target=$id;
            $friend->is_pending=1;

            $friend->save();

            $user=User::find($id);
            return response()->json(['user' => $user]);
        }
        return response()->json(null,401);

    }
}
