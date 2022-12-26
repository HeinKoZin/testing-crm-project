<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        if(session('success')){
            toast(Session::get('success'), "success");
        }
        if(session('error')){
            toast(Session::get('error'), "error");
        }
        return view('pages.dashobard.index');
    }
}
