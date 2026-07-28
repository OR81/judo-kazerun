<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers;

use App\Models\User;
use App\Modules\Auth\Http\Requests\RequestCodeRequest;
use App\Modules\Auth\Http\Requests\VerifyCodeRequest;
use App\Modules\Auth\Services\LoginCodeService;
use App\Support\PhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * ورود با شمارهٔ موبایل و کد یک‌بارمصرف.
 *
 * There is no password anywhere in the application, for any role — an
 * administrator signs in exactly the way an athlete does. That removes the whole
 * class of password problems (reuse, resets, leaked hashes) and matches how
 * people here actually expect to log in.
 *
 * Between the two steps the pending number lives in the session, never in the
 * URL or a hidden field, so the second step cannot be pointed at somebody else's
 * account by editing the request.
 */
class LoginController extends Controller
{
    private const SESSION_MOBILE = 'login.mobile';

    private const SESSION_REMEMBER = 'login.remember';

    public function __construct(private readonly LoginCodeService $codes) {}

    /** گام یکم — فرم شمارهٔ موبایل */
    public function create(Request $request): View|RedirectResponse
    {
        if ($user = $request->user()) {
            return redirect($user->role->home());
        }

        return view('auth.login');
    }

    /** ارسال کد */
    public function store(RequestCodeRequest $request): RedirectResponse
    {
        $mobile = $request->mobile();
        $user = User::query()->where('mobile', $mobile)->first();

        /*
         * An unknown number is told so plainly. It does leak whether somebody is a
         * member, but membership of a town's judo board is not a secret, and the
         * alternative — silently pretending to send a code — strands every member
         * who mistypes a digit with no way to tell what went wrong.
         */
        if (! $user) {
            return back()
                ->withInput()
                ->withErrors(['mobile' => 'این شماره در سامانه ثبت نشده است. اگر تازه ثبت‌نام کرده‌اید، با دفتر هیئت تماس بگیرید.']);
        }

        if (! $user->is_active) {
            return back()
                ->withInput()
                ->withErrors(['mobile' => 'حساب کاربری شما غیرفعال است. برای پیگیری با دفتر هیئت تماس بگیرید.']);
        }

        if ($this->codes->hasReachedHourlyLimit($mobile)) {
            return back()
                ->withInput()
                ->withErrors(['mobile' => 'تعداد درخواست‌های کد برای این شماره زیاد بوده است. یک ساعت دیگر دوباره تلاش کنید.']);
        }

        $code = $this->codes->issue($mobile, $request->ip());

        $request->session()->put(self::SESSION_MOBILE, $mobile);
        $request->session()->put(self::SESSION_REMEMBER, $request->boolean('remember'));

        return redirect()
            ->route('login.verify')
            ->with('status', 'کد ورود به شمارهٔ '.PhoneNumber::mask($mobile).' پیامک شد.')
            ->with('login_code', $this->exposed($code));
    }

    /** گام دوم — فرم کد */
    public function verify(Request $request): View|RedirectResponse
    {
        $mobile = $request->session()->get(self::SESSION_MOBILE);

        if (! PhoneNumber::isValid($mobile)) {
            return redirect()->route('login');
        }

        return view('auth.verify', [
            'mobile' => $mobile,
            'masked' => PhoneNumber::mask($mobile),
            'cooldown' => $this->codes->cooldown($mobile),
            'ttl' => (int) config('sms.code.ttl'),
            'length' => (int) config('sms.code.length'),
        ]);
    }

    /** بررسی کد و ورود */
    public function confirm(VerifyCodeRequest $request): RedirectResponse
    {
        $mobile = $request->session()->get(self::SESSION_MOBILE);

        if (! PhoneNumber::isValid($mobile)) {
            return redirect()->route('login');
        }

        if (! $this->codes->verify($mobile, $request->code())) {
            return back()->withErrors(['code' => 'کد واردشده نادرست یا منقضی شده است.']);
        }

        $user = User::query()->where('mobile', $mobile)->first();

        if (! $user || ! $user->is_active) {
            $this->forget($request);

            return redirect()->route('login')
                ->withErrors(['mobile' => 'حساب کاربری در دسترس نیست.']);
        }

        Auth::login($user, (bool) $request->session()->get(self::SESSION_REMEMBER));

        // A fresh session id on privilege change — the standard defence against
        // an attacker fixing the session before sign-in.
        $request->session()->regenerate();
        $this->forget($request);

        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        return redirect()->intended($user->role->home());
    }

    /** ارسال دوبارهٔ کد */
    public function resend(Request $request): RedirectResponse
    {
        $mobile = $request->session()->get(self::SESSION_MOBILE);

        if (! PhoneNumber::isValid($mobile)) {
            return redirect()->route('login');
        }

        if ($this->codes->cooldown($mobile) > 0) {
            return back()->withErrors(['code' => 'برای ارسال دوبارهٔ کد کمی صبر کنید.']);
        }

        if ($this->codes->hasReachedHourlyLimit($mobile)) {
            return back()->withErrors(['code' => 'تعداد درخواست‌های کد برای این شماره زیاد بوده است. یک ساعت دیگر دوباره تلاش کنید.']);
        }

        $code = $this->codes->issue($mobile, $request->ip());

        return back()
            ->with('status', 'کد تازه ارسال شد.')
            ->with('login_code', $this->exposed($code));
    }

    /** بازگشت به گام یکم برای اصلاح شماره */
    public function change(Request $request): RedirectResponse
    {
        $this->forget($request);

        return redirect()->route('login');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'از حساب کاربری خارج شدید.');
    }

    /**
     * The code itself, but only where there is no phone to read it from.
     *
     * Guarded by config so it can never be switched on by accident in production:
     * see the note on `sms.expose_codes`.
     */
    private function exposed(string $code): ?string
    {
        return config('sms.expose_codes') ? $code : null;
    }

    private function forget(Request $request): void
    {
        $request->session()->forget([self::SESSION_MOBILE, self::SESSION_REMEMBER]);
    }
}
