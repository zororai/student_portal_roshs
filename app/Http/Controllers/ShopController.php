<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)
            ->where('quantity', '>', 0);

        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Category filter
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Price filter
        if ($request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'name';
        $sortOrder = $request->sort_order ?? 'asc';
        
        if ($sortBy === 'price') {
            $query->orderBy('price', $sortOrder);
        } elseif ($sortBy === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('name', $sortOrder);
        }

        $products = $query->paginate(12);
        $categories = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('website.shop.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->findOrFail($id);

        // Get related products from same category
        $relatedProducts = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('website.shop.show', compact('product', 'relatedProducts'));
    }
}
