<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountTraceController extends Controller
{
    public function index()
    {
        return view('home.index', [
            'title' => 'Home',
            'subtitle' => 'Home',
        ]);
    }

    public function dailyreport()
    {
        return view('home.dailyreport', [
            'title' => 'Daily Report',
            'subtitle' => 'Daily Report',
        ]);
    }
}
