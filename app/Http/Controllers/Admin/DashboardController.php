<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users'        => User::count(),
            'active_ads'   => Ad::where('status', 'active')->count(),
            'pending_ads'  => Ad::where('status', 'pending')->count(),
            'open_reports' => Report::where('status', 'open')->count(),
            'total_ads'    => Ad::withTrashed()->count(),
            'categories'   => Category::roots()->count(),
        ];

        $adsByStatus = Ad::withTrashed()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentPending = Ad::where('status', 'pending')
            ->with(['user', 'category'])
            ->latest()
            ->take(8)
            ->get();

        $recentUsers = User::latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'adsByStatus', 'recentPending', 'recentUsers'));
    }
}
