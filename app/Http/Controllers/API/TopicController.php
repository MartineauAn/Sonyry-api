<?php

namespace App\Http\Controllers\API;

use App\Models\Categorie;
use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $topics = Topic::latest('created_at')->with(['user' , 'categorie'])->simplepaginate(8);
        $categories = Categorie::all();

        return response()->json([
            'topics' => $topics,
            'categories' => $categories
        ]);

    }

    public function byCategory($id)
    {
        return Topic::where('categorie_id',$id)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Categorie::all() ;

        return response()->json([
        'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $topic = new Topic();
        $topic->content = $request->input('content');
        $topic->title = $request->input('title');
        $topic->categorie_id = $request->input('categorie_id');
        $topic->user_id = Auth::id();

        return response()->json($topic->save());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $topic = Topic::find($id);
        $topic->user;
        $topic->comments;
        foreach($topic->comments as $comment) {
            $comment->user;
            $comment->comments;
            foreach ($comment->comments as $replyComment){
                $replyComment->user;
            }
        }

        return response()->json([
            'topic' => $topic
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $topic = Topic::find($id);

        if (Auth::user()->can('update', $topic)) {

            return response()->json([
                'topic' => $topic
            ]);
        }

        return response()->json(null,401);
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
        $topic = Topic::find($id);

        if (Auth::user()->can('update', $topic)) {

            $data = $request->validate([
                'title' => 'required|min:5',
                'content' => 'required|min:10'

            ]);

            $topic->update($data);
            return response()->json([
                'topic' => $topic,'status' => 'Success', 'Message' => 'Topic mis à jour'
            ]);
        }
        return response()->json(null,401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $topic = Topic::find($id);

        if (Auth::user()->can('delete', $topic)) {


            if (count($topic->comments) > 0) {
                foreach ($topic->comments as $comment) {
                    if(count($comment->comments) > 0 ) {
                        foreach ($comment->comments as $reply) {
                            $reply->delete();
                        }
                    }
                    $comment->delete();
                }
            }

            $topic->delete();
            return response()->json(['status' => 'Success', 'Message' => 'Topic supprimé']);
        }
        return response()->json(null,401);
    }
}
