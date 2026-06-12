<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ad::withTrashed()
            ->with(['user', 'category', 'primaryImage'])
            ->when($request->filled('status'),   fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'),        fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->orderByDesc('created_at');

        $ads        = $query->paginate(25)->withQueryString();
        $categories = Category::roots()->active()->orderBy('sort_order')->get();

        return view('admin.ads.index', compact('ads', 'categories'));
    }

    public function approve(Ad $ad): RedirectResponse
    {
        $ad->update(['status' => 'active']);
        return back()->with('status', "Ad #{$ad->id} approved.");
    }

    public function reject(Ad $ad): RedirectResponse
    {
        $ad->update(['status' => 'rejected']);
        return back()->with('status', "Ad #{$ad->id} rejected.");
    }

    public function toggleFeatured(Ad $ad): RedirectResponse
    {
        $ad->update(['is_featured' => ! $ad->is_featured]);
        $label = $ad->is_featured ? 'featured' : 'unfeatured';
        return back()->with('status', "Ad #{$ad->id} {$label}.");
    }

    public function destroy(Ad $ad): RedirectResponse
    {
        $ad->forceDelete();
        return back()->with('status', "Ad #{$ad->id} permanently deleted.");
    }
}
