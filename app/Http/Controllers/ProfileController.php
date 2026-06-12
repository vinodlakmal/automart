<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(): View
    {
        $user = auth()->user();

        $adStats    = $user->ads()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $savedCount = $user->favorites()->count();

        return view('profile.edit', compact('user', 'adStats', 'savedCount'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'regex:/^0\d{9}$/'],
        ], [
            'phone.regex' => 'Phone must be a valid Sri Lankan number (e.g. 0771234567).',
        ]);

        $user->update($validated);

        return back()->with('profile_status', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return back()->with('password_status', 'Password changed successfully.');
    }
}
