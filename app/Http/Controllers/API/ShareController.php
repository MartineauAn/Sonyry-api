<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ShareDirectory;
use App\Models\ShareGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    public function index($id)
    {
        return response()->json([
            'directories' => ShareDirectory::where('group_id', $id)->get(),
            'shares' => ShareGroup::where('group_id', $id)->with(['page' , 'page.user'])->get()
        ]);
    }

    public function directory($id)
    {
        return ShareDirectory::all()->find($id);
    }

    public function links($id)
    {
        $current = ShareDirectory::all()->find($id);

        $links = [];

        while ($current->shareDirectory_id != 0) {
            $links [] = $current;
            $current = ShareDirectory::find($current->shareDirectory_id);
        }
        $links [] = $current;
        $links = array_reverse($links);

        return response()->json($links);
    }

    public function pages($id)
    {
        $pages = Auth::user()->pages;

        foreach ($pages as $page) {
            $test = ShareGroup::where('page_id', $page->id)->where('group_id', $id)->get();
            if (count($test) > 0) {
                $page->isShared = true;
            } else {
                $page->isShared = false;
            }
        }

        return response()->json($pages);
    }


    public function storeDirectory(Request $request)
    {
        $newDirectory = new ShareDirectory;
        $newDirectory->name = $request->input('name');
        $newDirectory->group_id = $request->input('group_id');
        $newDirectory->shareDirectory_id = $request->input('directory_id');

        return response()->json($newDirectory->save());
    }

    public function destroyDirectory($id, $groupId)
    {
        $oldDirectory = ShareDirectory::find($id);

        $allDirectories = ShareDirectory::where('group_id', $groupId)->get();

        foreach ($allDirectories as $one) {
            if ($one->shareDirectory_id != 0) {
                $test = ShareDirectory::find($one->shareDirectory_id);
                if ($test == null) {
                    $one->delete();
                }
            }
        }

        $allShares = ShareGroup::where('group_id', $groupId)->get();

        foreach ($allShares as $one) {
            if ($one->shareDirectory_id != 0) {
                $test = ShareDirectory::find($one->shareDirectory_id);
                if ($test == null) {
                    $one->delete();
                }
            }
        }

        return response()->json($oldDirectory->delete());
    }

    public function sharePage(Request $request)
    {

        foreach ($request->input('shares') as $share) {
            $shareGroup = new ShareGroup();

            $shareGroup->user_id = Auth::user()->id;
            $shareGroup->page_id = $share;
            $shareGroup->group_id = $request->input('group_id');
            $shareGroup->shareDirectory_id = $request->input('directory_id');

            $shareGroup->save();
        }

        return response()->json(true);

    }

    public function destroyShare($id)
    {
        $shareGroup = ShareGroup::find($id);

        return response()->json($shareGroup->delete());
    }
}
