<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bloc;
use App\Models\CollectionsPage;
use App\Models\ImageAction;
use App\Models\Keyword;
use App\Models\KeywordPage;
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
    public function index($filter =null)
    {
        if ($filter != null){
            $keywords = explode(';',$filter);

            $keywords = Keyword::all()->whereIn('label' , $keywords)->pluck('id');

            $keywordPages = KeywordPage::all()->whereIn('keyword_id' , $keywords)->pluck('page_id');

            $pages = Page::all()->find($keywordPages);
        }
        else{
            $pages = Auth()->user()->pages;
        }

        if (count($pages) > 0){
            foreach ($pages as $page) {
                $folder = 'pages/'.$page->user_id;
                if ($page->image == 'default_page.png'){
                    $folder = 'default';
                }

                $page->updated_at_ = \Carbon\Carbon::parse($page->updated_at)->format('d/m/Y à H:h');

                $page->link = 'storage/'.$folder.'/'.$page->image;

                $page->keywords;

            }
        }

        return response()->json(['pages' => $pages]);
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

            /** Ajout des keywords */
            if ($request->input('keywords') != null) {
                $keywords = explode(';', $request->input('keywords'));

                foreach ($keywords as $keyword) {
                    $search = Keyword::where('label', $keyword)->get();
                    if (count($search) == 0) {
                        $new = new Keyword();
                        $new->label = $keyword;
                        $new->save();

                        $search = $new;
                    } else {
                        $search = $search[0];
                    }

                    $keywordPage = new KeywordPage();

                    $keywordPage->page_id = $page->id;

                    $keywordPage->keyword_id = $search->id;

                    $keywordPage->save();
                }
            }

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

            $page->keywords;

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

            $page->keywords;

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

            /** Update des keywords */

            if ($request->input('keywords') != null) {
                $keywords = explode(';', $request->input('keywords'));

                foreach ($keywords as $keyword) {
                    $search = Keyword::where('label', $keyword)->get();
                    if (count($search) == 0) {
                        $new = new Keyword();
                        $new->label = $keyword;
                        $new->save();

                        $search = $new;
                    } else {
                        $search = $search[0];
                    }

                    $keywordPageSearch = KeywordPage::where('page_id', $page->id)->where('keyword_id', $search->id)->get();

                    if (count($keywordPageSearch) == 0){
                        $keywordPage = new KeywordPage();

                        $keywordPage->page_id = $page->id;

                        $keywordPage->keyword_id = $search->id;

                        $keywordPage->save();
                    }

                }
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

            /** Suppression des keywords */

            KeywordPage::where('page_id' , $page->id)->delete();

            $page->delete();

            return response()->json(['message'=> 'success']);

        }

        return response()->json(null , 401);
    }
}
