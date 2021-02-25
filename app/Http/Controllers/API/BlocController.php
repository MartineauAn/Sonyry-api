<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\Bloc;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $page = Page::find($id);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id)
    {

        $page = Page::find($id);

        if (Auth::user()->can('createBloc', $page)) {
            $bloc = new Bloc();

            $type = $request->input('type');

            if ($type == 'text') {

                $bloc->text();

                $bloc->content = $request->input('content');

            } elseif ($type == 'script') {

                $bloc->script();

                $bloc->content = $request->input('content');
            } elseif ($type == 'image') {

                $bloc->image();

                if ($request->file('content')) {

                    $image = $request->file('content');

                    $mimeType = $image->getClientMimeType();

                    $imageFullName = $image->getClientOriginalName();
                    $imageName = pathinfo($imageFullName, PATHINFO_FILENAME);
                    $extension = $image->getClientOriginalExtension();
                    $file = time() . '_' . $imageName . '.' . $extension;

                    if (substr($mimeType, 0, 5) == 'image') {
                        $image->storeAs('public/bloc/' . $page->id . '/image', $file);
                        $bloc->content = $file;

                    } else {
                        return redirect()->route('page.edit')->with('danger', 'Le fichier que vous essayer de joindre n\'est pas toléré');
                    }

                } else {
                    return redirect()->route('page.edit')->with('danger', 'Veuillez insérer un fichier image');
                }
            } elseif ($type == 'video') {

                $bloc->video();

                if ($request->file('content')) {

                    $video = $request->file('content');

                    $mimeType = $video->getClientMimeType();

                    $fileFullname = $video->getClientOriginalName();
                    $fileName = pathinfo($fileFullname, PATHINFO_FILENAME);
                    $extension = $video->getClientOriginalExtension();
                    $file = time() . '_' . $fileName . '.' . $extension;

                    if (substr($mimeType, 0, 5) == 'video') {
                        $video->storeAs('public/bloc/' . $page->id . '/video/', $file);
                        $bloc->content = $file;
                    } else {
                        return redirect()->route('page.edit')->with('danger', 'Le fichier que vous essayer de joindre n\'est pas toléré');
                    }

                } else {
                    return redirect()->route('page.edit')->with('danger', 'Veuillez insérer un fichier video');
                }
            } elseif ($type == 'file') {
                $bloc->file();
                if ($request->file('content')) {

                    $fileInput = $request->file('content');

                    $size = $fileInput->getSize();

                    //if (1===1){
                    $fileFullName = $fileInput->getClientOriginalName();
                    $fileName = pathinfo($fileFullName, PATHINFO_FILENAME);
                    $extension = $fileInput->getClientOriginalExtension();
                    $file = time() . '_' . $fileName . '.' . $extension;
                    //}
                    $fileInput->storeAs('public/bloc/' . $page->id . '/file/', $file);
                    $bloc->content = $file;
                }

            }

            $bloc->page_id = $page->id;

            $bloc->title = $request->input('title');

            $bloc->save();

        }

        return response()->json($page);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
