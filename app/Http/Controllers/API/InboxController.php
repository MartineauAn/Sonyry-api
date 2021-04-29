<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inbox;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InboxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inboxes = Inbox::where('user_id', Auth::user()->id)->get();

        foreach ($inboxes as $inbox){
            $inbox->notification;
        }

        return response()->json([
            'inboxes' => $inboxes
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function toTrash($id)
    {
        /** Set the notification to trash */

        $notification = Notification::find($id);
        $notification->trash = 1;
        $notification->save();

        return response()->json();
    }
}
