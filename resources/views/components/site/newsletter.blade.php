<div class="border-b border-white/10">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
        <div class="flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div class="max-w-xl">
                <h2 class="flex items-center gap-3 text-lg font-bold text-white">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-accent text-on-accent">
                        <i class="fa-solid fa-envelope-open-text text-sm" aria-hidden="true"></i>
                    </span>
                    عضویت در خبرنامهٔ هیئت
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-on-ink-muted">
                    از اطلاعیهٔ ثبت‌نام کلاس‌ها، تاریخ آزمون‌های دان و برنامهٔ مسابقات پیش از همه باخبر شوید.
                </p>
            </div>

            <form action="{{ route('newsletter.subscribe') }}" method="POST"
                  class="w-full max-w-md shrink-0">
                @csrf

                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="flex-1">
                        <label for="newsletter-email" class="sr-only">نشانی رایانامه</label>
                        <input id="newsletter-email" type="email" name="email" required dir="ltr"
                               value="{{ old('email') }}"
                               placeholder="you@example.com"
                               autocomplete="email"
                               @error('email') aria-invalid="true" aria-describedby="newsletter-error" @enderror
                               class="w-full rounded-xl border border-white/15 bg-white/5 px-4 py-3 text-sm text-white
                                      placeholder:text-on-ink-muted/70 transition focus:border-accent focus:bg-white/10">
                    </div>

                    <button type="submit"
                            class="shrink-0 rounded-xl bg-brand px-6 py-3 text-sm font-bold text-on-brand transition hover:bg-brand-hover">
                        عضویت
                    </button>
                </div>

                @error('email')
                    <p id="newsletter-error" class="mt-2 text-xs text-red-300" role="alert">{{ $message }}</p>
                @enderror

                <p class="mt-2 text-xs text-on-ink-muted/75">
                    نشانی شما محرمانه می‌ماند و هر زمان می‌توانید لغو عضویت کنید.
                </p>
            </form>
        </div>
    </div>
</div>
