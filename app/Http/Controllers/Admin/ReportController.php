<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $reports = Report::with(['ad', 'user'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.reports.index', compact('reports'));
    }

    public function resolve(Request $request, Report $report): RedirectResponse
    {
        $request->validate(['resolution' => ['required', 'in:reviewed,dismissed']]);
        $report->update(['status' => $request->resolution]);
        return back()->with('status', "Report #{$report->id} marked as {$request->resolution}.");
    }
}
