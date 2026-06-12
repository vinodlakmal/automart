<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $ads = auth()->user()
            ->favoriteAds()
            ->with(['primaryImage', 'category', 'city', 'district'])
            ->orderByPivot('created_at', 'desc')
            ->paginate(20);

        return view('favorites.index', compact('ads'));
    }

    public function toggle(Ad $ad): JsonResponse|RedirectResponse
    {
        $user     = auth()->user();
        $existing = $user->favorites()->where('ad_id', $ad->id)->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
        } else {
            $user->favorites()->create(['ad_id' => $ad->id]);
            $favorited = true;
        }

        if (request()->expectsJson()) {
            return response()->json(['favorited' => $favorited]);
        }

        return back()->with('status', $favorited ? 'Saved to favorites.' : 'Removed from favorites.');
    }
}
