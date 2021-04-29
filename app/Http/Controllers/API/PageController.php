<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bloc;
use App\Models\CollectionsPage;
use App\Models\ImageAction;
use App\Models\Page;
use App\Models\ShareGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = Auth()->user()->pages;

        foreach ($pages as $page) {
            $folder = 'pages/'.$page->user_id;
            if ($page->image == 'default_page.png'){
                $folder = 'default';
            }

            $page->updated_at_ = \Carbon\Carbon::parse($page->updated_at)->format('d/m/Y à H:h');

            $page->link = 'storage/'.$folder.'/'.$page->image;


        }

        return response()->json($pages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $page = new Page();

        $page->title = $request->input('title');
        $page->description = $request->input('description');
        $page->user_id = Auth::user()->id;

        if ($request->file('image')) {
            $image = $request->file('image');

            $imageAction = new ImageAction();

            $file = $imageAction->store($image, 'pages');

        } else {
            $file = 'default_page.png';
        }

        $page->image = $file;

        if($page->save()){
            return response()->json([]);
        }
        return response()->json([] , 500);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = Page::find($id);

        if (Auth::user()->can('view', $page)) {

            $folder = 'pages/'.$page->user_id;
            if ($page->image == 'default_page.png'){
                $folder = 'default';
            }

            $page->updated_at_ = \Carbon\Carbon::parse($page->updated_at)->format('d/m/Y à H:h');

            $page->link = 'storage/'.$folder.'/'.$page->image;

            foreach($page->blocs as $bloc){
                switch ($bloc->type){
                    case 'file':
                    case 'image':
                    case 'video':


                        $bloc->link = 'storage/bloc/'.$page->id.'/'.$bloc->type.'/'.$bloc->content;

                        break;
                }
            }

            return response()->json($page);
        }

        return response()->json(null, 401);
    }

    public function edit($id)
    {
        $page = Page::find($id);

        if (Auth::user()->can('update', $page)) {

            $folder = 'pages/'.$page->user_id;
            if ($page->image == 'default_page.png'){
                $folder = 'default';
            }

            $page->updated_at_ = \Carbon\Carbon::parse($page->updated_at)->format('d/m/Y à H:h');

            $page->link = 'storage/'.$folder.'/'.$page->image;

            return response()->json($page);
        }

        return response()->json(null, 401);
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

        $page = Page::find($id);

        if (Auth::user()->can('update', $page)) {

            if ($request->input('title') != null) {
                $page->title = $request->input('title');
            }

            if ($request->input('description') != null) {
                $page->description = $request->input('description');
            }

            if ($request->file('image')) {

                /** update de l'image */

                /**  suppression de l'ancienne image */
                $fileToDelete = 'public/pages/' . Auth::user()->id . '/' . $page->image;

                $imageAction = new ImageAction();

                $imageAction->deleteImage($fileToDelete);

                $image = $request->file('image');

                $file = $imageAction->store($image, 'pages');

                $page->image = $file;
            }

            $page->save();

            return response()->json($page);

        }

        return response()->json(null , 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $page = Page::find($id);

        if (Auth::user()->can('delete', $page)) {

            CollectionsPage::where('page_id', $id)->delete();

            /** delete the page in the collection if they are in any collection */

            ShareGroup::where('page_id', $page->id)->delete();

            $blocs = Bloc::where('page_id', $page->id)->get();

            if (count($blocs) > 0) {
                foreach ($blocs as $bloc) {
                    Bloc::deleteFromStorage($bloc);
                }
            }

            Bloc::where('page_id', $page->id)->delete();

            $fileToDelete = 'public/pages/' . Auth::user()->id . '/' . $page->image;

            $imageAction = new ImageAction();

            $imageAction->deleteImage($fileToDelete);

            $page->delete();

            return response()->json(['message'=> 'success']);

        }

        return response()->json(null , 401);
    }
}
