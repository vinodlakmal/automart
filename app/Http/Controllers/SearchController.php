<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q         = trim($request->query('q', ''));
        $sort      = $request->query('sort', '');
        $validSorts = ['price_asc', 'price_desc', 'views'];

        $base = Ad::query()
            ->active()
            ->search($q ?: null)
            ->when($request->filled('category'), fn ($q) => $q->inCategory($request->integer('category')))
            ->when($request->filled('district'),  fn ($q) => $q->inDistrict($request->integer('district')))
            ->priceBetween($request->query('min_price'), $request->query('max_price'))
            ->when($request->query('condition'),  fn ($q, $c) => $q->where('condition', $c));

        // Category hit-counts (before pagination, before sort).
        $hitCounts = (clone $base)
            ->selectRaw('category_id, count(*) as cnt')
            ->groupBy('category_id')
            ->pluck('cnt', 'category_id');

        $results = (clone $base)
            ->with(['primaryImage', 'category', 'district', 'city'])
            ->orderByDesc('is_featured')
            ->when($sort === 'price_asc',               fn ($q) => $q->orderBy('price'))
            ->when($sort === 'price_desc',              fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'views',                   fn ($q) => $q->orderByDesc('views'))
            ->when(! in_array($sort, $validSorts),      fn ($q) => $q->orderByDesc('created_at'))
            ->paginate(15)
            ->withQueryString();

        $categories  = Category::roots()->active()->orderBy('sort_order')->get();
        $districts   = District::orderBy('name')->get();
        $favoriteIds = auth()->check()
            ? auth()->user()->favorites()->pluck('ad_id')->all()
            : [];

        return view('search.results', compact('results', 'q', 'categories', 'districts', 'hitCounts', 'favoriteIds'));
    }
}
