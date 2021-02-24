<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Topic;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store (Request $request,$id)
    {
        $request->validate([
            'content' => 'required|min:5'
        ]);

        $topic = Topic::find($id);

        $comment = new Comment();
        $comment->content = $request->input('content');

        $comment->user_id = auth()->user()->id;
        $comment->commentable_id = $topic->id;
        $topic->comments()->save($comment);



        return response()->json($topic);
    }

    public function storeCommentReply(Request $request ,$id)
    {
        $request->validate([
            'replyComment' => 'required|min:3'
        ]);

        $comment = Comment::find($id);
        $commentReply = new Comment();
        $commentReply->content = $request->input('replyComment');
        $commentReply->user_id = auth()->user()->id;
        $commentReply->commentable_id = $id;

        return response()->json($comment->comments()->save($commentReply));
    }
}
