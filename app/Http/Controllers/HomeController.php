<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function features()
    {
        return view('features');
    }

    public function download()
    {
        return view('download');
    }

    public function login(Request $request)
    {
        // Handle login logic here
        return redirect()->back()->with('success', 'Login berhasil!');
    }
}