# هیئت جودو شهرستان کازرون

وب‌سایت رسمی هیئت جودو کازرون — ۱۲ صفحهٔ عمومی فارسی/RTL، ثبت‌نام آنلاین با پرداخت اینترنتی،
پرتال ورزشکار و مربی، و پنل مدیریت کامل.

---

## پشتهٔ فناوری

| لایه | فناوری |
|---|---|
| بک‌اند | Laravel 13 · PHP 8.4 · MySQL 8.4 |
| فرانت‌اند | Blade · Tailwind CSS v4 · وانیلا JavaScript |
| بیلد | Vite 8 (Node.js) |
| احراز هویت | Laravel Fortify |
| پنل مدیریت | Filament v5 |
| تاریخ شمسی | morilog/jalali |
| تست | Pest 4 · Pint |

**سمت عمومی سایت هیچ فریم‌ورک جاوااسکریپتی ندارد.** Livewire و Alpine فقط داخل باندل
`/admin` بارگذاری می‌شوند و هرگز به صفحات عمومی نمی‌رسند.

---

## راه‌اندازی

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# یک دیتابیس MySQL به نام judo بسازید، سپس:
php artisan migrate:fresh --seed

npm run build     # یا npm run dev برای توسعه
php artisan serve
```

### حساب‌های نمونه

رمز عبور همه: `password` — ورود با شمارهٔ موبایل یا رایانامه ممکن است.

| نقش | موبایل | مقصد پس از ورود |
|---|---|---|
| مدیر | `09171234567` | `/admin` (Filament) |
| مربی | `09171112233` | `/coach` |
| ورزشکار | `09173334455` | `/dashboard` |

---

## ساختار

```
app/
├─ Enums/               واژگان دامنه با برچسب فارسی (رده سنی، وضعیت، مدال، …)
├─ Filament/            پنل مدیریت — Resources و Widgets
├─ Models/              ۲۲ مدل Eloquent
├─ Support/             PersianNumber · JalaliDate · MediaPlaceholder
└─ Modules/             ماژول‌های دامنه، هم‌راستا با ساختار پروژهٔ mazar
   ├─ Core/             helpers.php سراسری + HomeController
   ├─ Content/ Event/ Training/ People/ Media/ Download/ Contact/
   ├─ Registration/     ویزارد ثبت‌نام + EnrollmentService
   ├─ Payment/          قرارداد PaymentGateway + درایورها
   └─ Auth/             پرتال‌ها، میدل‌ور نقش، پاسخ‌های ورود و خروج

resources/
├─ css/app.css          دیزاین‌سیستم: توکن‌ها، دارک‌مود، یوتیلیتی‌ها
├─ js/                  ماژول‌های وانیلا JS (نقطهٔ ورود: app.js)
├─ fonts/               Vazirmatn variable — self-hosted
└─ views/
   ├─ layouts/          app · portal
   ├─ components/       ui/ · site/ · cards/ · icons/
   ├─ pages/            ۱۲ صفحهٔ عمومی
   └─ portal/           داشبورد ورزشکار و مربی

scripts/                ابزارهای راستی‌آزمایی (پایین)
```

---

## تصمیم‌های طراحی

### پالت رنگ

چهار رنگ برند (`#111827`، `#DC2626`، `#F59E0B`، `#F8FAFC`) به‌ظاهر با پالت پیش‌فرض Tailwind
یکی به‌نظر می‌رسند، **اما نیستند**: Tailwind v4 پالت خود را برای گاموت وسیع بازتنظیم کرده و
`red-600` اکنون `#e7000b` و `amber-500` برابر `#fe9a00` است.

بنابراین رمپ‌های `crimson` و `gold` با `scripts/brand-scale.mjs` ساخته می‌شوند: شکل رمپ از
Tailwind وام گرفته می‌شود ولی چنان بازهدف‌گذاری می‌شود که `crimson-600` دقیقاً `#DC2626`
و `gold-500` دقیقاً `#F59E0B` باشد.

```bash
npm run brand:scale     # تولید مجدد رمپ‌ها
npm run check:contrast  # بررسی WCAG همهٔ جفت‌رنگ‌ها
```

`check:contrast` اگر هر جفتی زیر حد مجاز بیفتد با کد خطا خارج می‌شود.

### تاریخ و اعداد

همه‌چیز میلادی ذخیره می‌شود و تبدیل فقط در لایهٔ نمایش رخ می‌دهد.

```blade
{{ shamsi($news->published_at) }}            ← ۵ مرداد ۱۴۰۵
{{ shamsi($event->starts_at, 'full') }}      ← یکشنبه ۵ مرداد ۱۴۰۵
{{ fa_number(1250000) }}                     ← ۱٬۲۵۰٬۰۰۰
{{ toman(450000) }}                          ← ۴۵۰٬۰۰۰ تومان
```

> نام تابع `shamsi()` است نه `jdate()` — چون `morilog/jalali` خودش یک `jdate()` سراسری
> تعریف می‌کند و autoload آن بر پکیج ریشه اولویت دارد.

منطقهٔ زمانی روی `Asia/Tehran` تنظیم شده؛ توجه کنید که `config/app.php` پیش‌فرض Laravel
مقدار `UTC` را هاردکد می‌کند و `APP_TIMEZONE` را نمی‌خواند.

### تصاویر

دادهٔ نمونه به سرویس‌های تصویر ریموت اشاره می‌کند (`app/Support/MediaPlaceholder.php`).
`images.unsplash.com` از ایران قابل دسترس نیست، بنابراین از picsum و pravatar استفاده شده است.

پس از آپلود عکس واقعی از پنل مدیریت، همان ستون‌ها مسیر نسبی دیسک را نگه می‌دارند و
`ResolvesMedia` هر دو حالت را می‌شناسد — بدون هیچ تغییری در کد فراخوان.

### پرداخت

مبالغ همه‌جا به **تومان** ذخیره می‌شوند. تعویض درگاه فقط با یک کلید در `.env`:

```env
PAYMENT_GATEWAY=fake        # توسعه: صفحهٔ شبیه‌ساز داخل برنامه
PAYMENT_GATEWAY=zarinpal    # واقعی
ZARINPAL_MERCHANT_ID=...
ZARINPAL_SANDBOX=true
```

افزودن زیبال / آیدی‌پی / ملت یعنی یک کلاس درایور جدید و یک کلید در `config/payment.php`.
`verify()` باید idempotent بماند — درگاه‌ها callback را تکرار می‌کنند و کاربر صفحهٔ بازگشت
را رفرش می‌کند.

### دسترس‌پذیری

- کنتراست همهٔ جفت‌رنگ‌ها ماشینی بررسی می‌شود
- حلقهٔ فوکوس از `gold-600` است نه `gold-500` (نسبت ۳:۱ لازم برای WCAG 1.4.11)
- دکمهٔ برند در دارک‌مود روی `crimson-600` می‌ماند (سفید روی `crimson-500` فقط ۳٫۷۶:۱ است)
- قفل فوکوس در دراور موبایل، جستجو و لایت‌باکس
- فلش‌های کیبورد در اسلایدر و لایت‌باکس برای RTL آینه شده‌اند
- احترام کامل به `prefers-reduced-motion`
- بدون جاوااسکریپت هم همهٔ محتوا دیده می‌شود و فرم ثبت‌نام کار می‌کند

---

## راستی‌آزمایی

```bash
php artisan test          # ۷۴ تست
./vendor/bin/pint --test  # قالب‌بندی کد

php scripts/smoke.php         # بارگذاری همهٔ مدل‌ها و enumها
php scripts/render.php        # رندر همهٔ مسیرهای عمومی
php scripts/admin-check.php   # رندر همهٔ صفحات پنل مدیریت
npm run check:contrast        # کنتراست WCAG
```

---

## نکات نگهداری

- **ناوبری** در `config/navigation.php` تعریف شده — مگامنو، دراور موبایل و فوتر همگی از
  همان یک منبع می‌خوانند.
- **تنظیمات سایت** (نشانی، تلفن، شبکه‌های اجتماعی، ساعات کاری) در جدول `settings` است و
  با `setting('key')` خوانده می‌شود؛ کل جدول یک‌جا کش می‌شود.
- **`Model::shouldBeStrict()`** در محیط local فعال است و مشکل N+1 را به‌صورت خطا نشان می‌دهد.
- **نقشه**: به‌جای Google Maps یک جای‌گزین ایستا گذاشته شده تا سایت به اسکریپت شخص ثالث
  وابسته نباشد. برای نقشهٔ زنده، بلوک داخل `pages/contact.blade.php` را با نشان یا بلد جایگزین کنید.
- **آیکون‌های برند** (اینستاگرام، تلگرام، واتس‌اپ) SVG درون‌خطی هستند تا وب‌فونت
  ۱۱۳ کیلوبایتی `fa-brands` هرگز بارگذاری نشود.
