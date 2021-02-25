<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inbox;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $notification = Notification::find($id);
        Inbox::where('notification_id', $notification->id)->delete();
        /** Delete the notification and the link of notification with the user */
        $notification->delete();

        return response()->json();
    }

    /**
     * Auto-generation of notification
     */
    public static function notificationAuto($title, $paragraph) {

        /** Generating the content of the notification */
        $notification = new Notification();
        $notification->title = $title;
        $notification->paragraph = $paragraph;
        $notification->save();

        /** Generation of the notification link with the user in question */
        $inbox = New Inbox();
        $inbox->notification_id = $notification->id;
        $inbox->user_id = Auth::user()->id;
        $inbox->save();
    }

    /**
     * Auto-generation of notification kicking
     */
    public static function notificationAutoKick($title, $paragraph, $member) {

        /** Generating the content of the notification */
        $notification = new Notification();
        $notification->title = $title;
        $notification->paragraph = $paragraph;
        $notification->save();

        /** Generation of the notification link with the user in question */
        $inbox = New Inbox();
        $inbox->notification_id = $notification->id;
        $inbox->user_id = $member;
        $inbox->save();
    }

    /**
     * Auto-generation of notification for invitation in group
     */
    public static function notificationAutoInviteGroup($title, $paragraph, $user_id, $group) {

        /** Generating the content of the notification */
        $notification = new Notification();
        $notification->title = $title;
        $notification->paragraph = $paragraph;
        $notification->save();

        $updateNotification = Notification::latest()->first();
        $updateNotification->link = route('group.accept', [
            'id' => $group->id,
            'notification' => $updateNotification->id ]);
        $updateNotification->save();

        /** Generation of the notification link with the user in question */
        $inbox = New Inbox();
        $inbox->notification_id = $notification->id;
        $inbox->user_id = $user_id;
        $inbox->save();
    }
}
