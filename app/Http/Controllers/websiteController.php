<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Newsletter;

class websiteController extends Controller
{
    public function index()
    {

 
    
     return view('website.index');
    }

    public function about()
    {
     return view('website.about');
    }

    public function contact()
    {
     return view('website.contact');
    }

    public function courses()
    {
     return view('website.courses');
    }

    public function news()
    {
    $newsletters = Newsletter::where('is_published', false)->latest()->get();
    return view('website.News', compact('newsletters'));

    }

    public function results()
    {
     return view('website.results');
    }

    public function success()
    {
     return view('website.success');
    }
    
}
