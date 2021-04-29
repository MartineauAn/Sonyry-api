<?php

namespace App\Http\Controllers\API;

use App\Models\CollectionsPage;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\ImageAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{

    public function index()
    {
        $collections = Auth::user()->collections;

        foreach ($collections as $collection) {
            $collection->pages;

            $folder = 'collections/'.$collection->user_id;
            if ($collection->image == 'default_collection.jpg'){
                $folder = 'default';
            }

            $collection->link = 'storage/'.$folder.'/'.$collection->image;
        }

        return response()->json($collections);
    }

    public function store(Request $request)
    {
        $collection = new Collection();

        $collection->name = $request->input('name');
        $collection->description = $request->input('description');
        $collection->user_id = Auth::user()->id;


        if ($request->file('image')) {

            $imageAction = new ImageAction();

            $image = $request->file('image');

            $file = $imageAction->store($image, 'collections');


        } else {
            $file = 'default_collection.jpg';
        }
        $collection->image = $file;

        return response()->json($collection->save());
    }

    public function show($id)
    {
        $collection = Collection::find($id);

        if (Auth::user()->can('update', $collection)) {

            $folder = 'collections/'.$collection->user_id;
            if ($collection->image == 'default_collection.jpg'){
                $folder = 'default';
            }

            $collection->link = 'storage/'.$folder.'/'.$collection->image;

            foreach ($collection->pages as $collectionPage) {

                $collectionPage->page;

                $folder = 'pages/'.$collectionPage->page->user_id;
                if ($collectionPage->page->image == 'default_page.png'){
                    $folder = 'default';
                }
                $collectionPage->page->updated_at_ = \Carbon\Carbon::parse($collectionPage->page->updated_at)->format('d/m/Y à H:h');

                $collectionPage->page->link = 'storage/'.$folder.'/'.$collectionPage->page->image;
            }

            return response()->json($collection);
        }

        return response()->json([] , 401);

    }

    public function update(Request $request , $id)
    {
        $collection = Collection::find($id);

        if (Auth::user()->can('update', $collection)) {

            if ($request->input('name') != null) {
                $collection->name = $request->input('name');
            }
            if ($request->input('description') != null) {
                $collection->description = $request->input('description');
            }
            if ($request->file('image')) {
                //delete old image
                $fileToDelete = 'public/collections/' . Auth::user()->id . '/' . $collection->image;

                $image = $request->file('image');

                $imageAction = new ImageAction();

                //Delete the old image
                $imageAction->deleteImage($fileToDelete);

                //Add the new image

                $collection->image = $imageAction->store($image, 'collections');
            }
            return response()->json($collection->save());
        }

        return response()->json(null , 401);
    }

    public function destroy($id)
    {
        $collection = Collection::find($id);

        if (Auth::user()->can('delete', $collection)) {

            // delete all the link between the page in the collection and the colletion

            CollectionsPage::where('collection_id', $collection->id)->delete();

            $imageAction = new ImageAction();

            $fileToDelete = 'public/collections/' . Auth::user()->id . '/' . $collection->image;

            $imageAction->deleteImage($fileToDelete);

            return response()->json($collection->delete());
        }

        return response()->json(null , 401);
    }
}
