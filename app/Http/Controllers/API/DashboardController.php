<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json(['pages' => Auth::user()->pages , 'collections' => Auth::user()->collections]);
    }
}
