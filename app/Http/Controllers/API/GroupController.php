<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Inbox;
use App\Models\InvitationGroup;
use App\Models\Notification;
use App\Models\ShareDirectory;
use App\Models\ShareGroup;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Auth::user()->groups;
        if (count($groups) > 0) {
            foreach ($groups as $group) {
                $group->members = UserGroup::all()->where('group_id', $group->id)->count();
            }
        }

        return response()->json(['groups' => $groups]);

    }

    public function store(Request $request)
    {
        $group = new Group();
        $group->name = $request->input('name');
        $group->user_id = Auth::user()->id;
        $group->save();

        $userGroup = new UserGroup();
        $userGroup->user_id = Auth::user()->id;
        $userGroup->group_id = $group->id;

        return response()->json($userGroup->save());
    }

    public function show($id)
    {
        $group = Group::find($id);

        if (Auth::user()->can('view', $group)) {

            $users = User::all();

            $members = UserGroup::where('group_id', $group->id)->with(['user'])->get();

            $availables = [];

            foreach ($users as $user) {

                $userGroup = UserGroup::all()->where('user_id', $user->id)->where('group_id', $id)->count();

                $invits = InvitationGroup::all()->where('user_id', $user->id)->where('group_id', $id)->count();

                if ($userGroup + $invits == 0) {
                    $availables [] = $user;
                }

            }

            return response()->json(['group' => $group, 'members' => $members, 'users' => $availables]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function update(Request $request, $id)
    {
        $group = Group::find($id);

        if (Auth::user()->can('update', $group)) {
            if ($request->input('name') != null) {
                $group->name = $request->input('name');
            }
            return response()->json($group->save());
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function destroy($id)
    {
        $group = Group::find($id);

        if (Auth::user()->can('delete', $group)) {
            UserGroup::where('group_id', $group->id)->delete();

            //delete the share directories from the group
            ShareDirectory::where('group_id', $group->id)->delete();

            ShareGroup::where('group_id', $group->id)->delete();

            // and delete the group
            return response()->json($group->delete());
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function exit($id)
    {
        $userGroups = UserGroup::where('user_id', Auth::user()->id)->where('group_id', $id)->get();

        $group = Group::find($id);


        if (Auth::user()->can('exit', $group)) {

            ShareGroup::where('user_id', Auth::user()->id)->where('group_id', $group->id)->delete();

            NotificationController::notificationAuto("Vous venez de quitter le groupe " . $userGroups[0]->group->name, "Bonjour, voici un mail vous informant que vous venez de quitter le groupe " . $userGroups[0]->group->name . ".");

            return response()->json($userGroups[0]->delete());
        }


    }

    public function kick($id, $user_id)
    {
        $group = Group::find($id);

        if (Auth::user()->can('kick', $group)) {

            $member = UserGroup::where('group_id', $id)->where('user_id', $user_id)->get();

            //delete share
            ShareGroup::where('user_id', Auth::user()->id)->where('group_id', $group->id)->delete();

            NotificationController::notificationAutoKick("Vous venez d'être exclue du groupe " . $group->name, "Bonjour, voici un mail vous informant que vous venez d'être exclue du groupe " . $group->name . ".", $member[0]->user->id);

            return response()->json($member[0]->delete());
        }

        return response()->json(false, 401);
    }

    public function invite($id, $user_id)
    {
        $group = Group::find($id);
        // If user have rights he create a new invitation
        if (Auth::user()->can('invite', $group)) {

            if (count(UserGroup::where('user_id', $user_id)->get()) > 0) {
                return response()->json(false, 401);
            }

            $invitation = new InvitationGroup();

            $invitation->user_id = $user_id;

            $invitation->group_id = $group->id;


            // Auto generate mail/notification
            NotificationController::notificationAutoInviteGroup('Invitation à rejoindre ' . $group->name, 'Bonjour, voici un mail vous informant que vous venez d\'être inviter à rejoindre ' . $group->name .
                '. Vous pouvez choisir de rejoindre ce groupe en cliquant sur le bouton rejoindre ou ignorer cette notification et là supprimer.', $user_id, $group);


            return response()->json($invitation->save());
        }

        return response()->json(false, 401);
    }

    public function accept($id , $notificationId)
    {
        $group = Group::find($id);
        $userGroup = UserGroup::where('user_id', Auth::user()->id)->where('group_id', $group->id)->get();

        if (count($userGroup) == 0) {
            // User joining the group
            $newUserGroup = new UserGroup();
            $newUserGroup->user_id = Auth::user()->id;
            $newUserGroup->group_id = $group->id;


            // Delete the invitationGroup for clean the bdd and so as not to pollute
            Notification::find($notificationId)->delete();
            Inbox::where('notification_id', $notificationId)->delete();
            InvitationGroup::where('user_id', Auth::user()->id)->where('group_id', $group->id)->delete();

            return response()->json($newUserGroup->save());
        }
        return response()->json(false, 401);
    }
}
