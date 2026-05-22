<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\HomePageService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $homePageService;

    public function __construct(HomePageService $homePageService)
    {
        $this->homePageService = $homePageService;
    }

    public function index()
    {
        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        $newProducts = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        // Obține secțiunile homepage configurate
        $homepageSections = $this->homePageService->getHomePageData();

        return view('frontend.home.index', compact(
            'featuredProducts',
            'newProducts',
            'categories',
            'homepageSections'
        ));
    }
}