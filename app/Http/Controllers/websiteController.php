<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Newsletter;
use App\Banner;
use App\StudentAchievement;

class websiteController extends Controller
{
    public function index()
    {
        $banner = Banner::first();
        
        if (!$banner) {
            // Create a default banner object with properties
            $banner = (object) [
                'image_path_1' => 'banners/default-banner.jpg',
                'image_path_2' => 'banners/default-banner.jpg',
                'image_path_3' => 'banners/default-banner.jpg'
            ];
        }
        
        $achievements = StudentAchievement::active()->get();
        
        return view('website.index', compact('banner', 'achievements'));
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
