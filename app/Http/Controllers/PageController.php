<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\District;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $stats = [
            'ads'       => Ad::active()->count(),
            'users'     => User::count(),
            'categories'=> Category::roots()->active()->count(),
            'districts' => District::count(),
        ];

        return view('pages.about', compact('stats'));
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        // Log the message (MAIL_MAILER=log); swap for a real mailer in production.
        Mail::raw(
            "From: {$data['name']} <{$data['email']}>\n\n{$data['message']}",
            fn ($m) => $m->to(config('mail.from.address'))->subject("[Merkei Mart] {$data['subject']}")
        );

        return back()->with('contact_success', true);
    }
}
