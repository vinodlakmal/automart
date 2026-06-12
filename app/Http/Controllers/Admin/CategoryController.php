<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('ads')
            ->with(['children' => fn ($q) => $q->withCount('ads')])
            ->roots()
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function toggleActive(Category $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);
        $label = $category->is_active ? 'enabled' : 'disabled';
        return back()->with('status', "Category \"{$category->name}\" {$label}.");
    }
}
