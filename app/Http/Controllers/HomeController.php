<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Category;

class HomeController extends Controller
{
    /**
     * Marketplace homepage: category grid + a section of recent ads per top category.
     */
    public function index()
    {
        $categories = Category::roots()
            ->active()
            ->withCount(['ads' => fn ($q) => $q->where('status', 'active')])
            ->orderBy('sort_order')
            ->get();

        // For each top category, grab a few recent active ads (including those in subcategories).
        $sections = $categories->map(function (Category $cat) {
            $childIds = $cat->children()->pluck('id')->push($cat->id);

            $ads = Ad::query()
                ->active()
                ->whereIn('category_id', $childIds)
                ->with('primaryImage')
                ->latest()
                ->take(6)
                ->get();

            return ['category' => $cat, 'ads' => $ads];
        })->filter(fn ($s) => $s['ads']->isNotEmpty())->values();

        $featured = Ad::query()
            ->active()
            ->featured()
            ->with(['primaryImage', 'city', 'district'])
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('categories', 'sections', 'featured'));
    }
}
