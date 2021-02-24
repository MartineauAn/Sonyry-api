<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cat = new Categorie();
        if (Auth::user()->can('viewAny', $cat)) {
            $categories = Categorie::all();
            return response()->json([
                'categories' => $categories
            ]);
        }
        return response()->json(null,401);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cat = new Categorie();
        if (Auth::user()->can('update', $cat)) {
            return response()->json();
        }
        return response()->json(null,401);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        request()->validate([
            'libelle' => 'required|min:5',
        ]);

        $categorie = new Categorie();

        $categorie->libelle = request()->input('libelle');

        if (Auth::user()->can('update', $categorie)) {

            $categorie->save();

            return redirect()->route('topics.index', $categorie->id);
        }
        return redirect()->route('home')->with('danger', 'Vous ne pouvez pas effectuer cette action');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $categorie = Categorie::find($id);

        if (Auth::user()->can('delete', $categorie)) {

            if (count($categorie->topics) > 0) {
                foreach ($categorie->topics as $topic) {

                    if (count($topic->comments) > 0) {
                        foreach ($topic->comments as $comment) {
                            if (count($comment->comments) > 0) {
                                foreach ($comment->comments as $reply) {
                                    $reply->delete();
                                }
                            }
                            $comment->delete();
                        }
                    }

                    $topic->delete();
                }
            }

            $categorie->delete();

            return redirect()->route('categorie.index');
        }

        return redirect()->route('home')->with('danger', 'Vous ne pouvez pas effectuer cette action');
    }
}
