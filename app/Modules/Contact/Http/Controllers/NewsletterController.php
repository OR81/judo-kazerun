<?php

declare(strict_types=1);

namespace App\Modules\Contact\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): RedirectResponse
    {
        $data = $request->validate(
            ['email' => ['required', 'email:rfc', 'max:180']],
            [],
            ['email' => 'نشانی رایانامه'],
        );

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $data['email']]);

        // Re-subscribing after an unsubscribe should just work.
        $subscriber->fill([
            'confirmed_at' => $subscriber->confirmed_at ?? now(),
            'unsubscribed_at' => null,
        ])->save();

        return back()->with('success', 'عضویت شما در خبرنامه ثبت شد.');
    }

    public function unsubscribe(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->update(['unsubscribed_at' => now()]);

        return redirect()->route('home')->with('status', 'عضویت شما در خبرنامه لغو شد.');
    }
}
