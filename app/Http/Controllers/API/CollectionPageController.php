<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\CollectionsPage;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $collection = Collection::find($id);

        if (Auth::user()->can('update', $collection)) {

            $pagesInCollection = CollectionsPage::where('collection_id', $id)->get();

            $pagesAvailables = Page::where('user_id', Auth::user()->id)->get();

            foreach ($pagesAvailables as $page) {

                $folder = 'pages/'.$page->user_id;
                if ($page->image == 'default_page.png'){
                    $folder = 'default';
                }

                $page->available = true;

                $page->link = 'storage/'.$folder.'/'.$page->image;

                foreach ($pagesInCollection as $pageChecking) {
                    if ($pageChecking->page_id == $page->id) {
                        $page->available = false;
                    }
                }
            }

            foreach($collection->pages as $page){

                $folder = 'pages/'.$page->page->user_id;
                if ($page->page->image == 'default_page.png'){
                    $folder = 'default';
                }


                $page->page->link = 'storage/'.$folder.'/'.$page->page->image;
            }

            $collection->availables = $pagesAvailables;

            return response()->json($collection);
        }

        return response()->json(null , 401);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id)
    {
        $collection = Collection::find($id);

        if (Auth::user()->can('update', $collection)) {
            foreach ($request->input('checkbox') as $item) {
                $collectionPage = new CollectionsPage();

                $collectionPage->collection_id = $id;
                $collectionPage->page_id = $item;

                $collectionPage->save();
            }

            return response()->json($collectionPage);
        }

        return response()->json(null, 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $collection = Collection::find($id);

        if (Auth::user()->can('delete', $collection)) {
            foreach ($request->input('checkbox') as $item) {
                CollectionsPage::where('page_id', $item)->delete();
            }
            return response()->json($collection);
        }

        return response()->json(null, 401);
    }
}
