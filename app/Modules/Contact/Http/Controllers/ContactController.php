<?php

declare(strict_types=1);

namespace App\Modules\Contact\Http\Controllers;

use App\Models\ContactMessage;
use App\Modules\Contact\Http\Requests\ContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact');
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        ContactMessage::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'پیام شما ثبت شد. همکاران ما در اولین فرصت پاسخ خواهند داد.');
    }
}
