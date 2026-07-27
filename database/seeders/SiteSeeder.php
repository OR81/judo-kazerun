<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\Slider;
use App\Models\Sponsor;
use App\Support\MediaPlaceholder;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        $this->sliders();
        $this->sponsors();
        $this->inbox();
    }

    private function sliders(): void
    {
        $slides = [
            [
                'eyebrow' => 'هیئت جودو شهرستان کازرون',
                'title' => 'راه پهلوانی از تاتامی آغاز می‌شود',
                'subtitle' => 'آموزش اصولی جودو زیر نظر مربیان رسمی فدراسیون',
                'description' => 'از نخستین گام روی تاتامی تا سکوی قهرمانی کشور؛ در کنار شما هستیم.',
                'cta_label' => 'ثبت‌نام آنلاین',
                'cta_url' => '/register',
                'secondary_cta_label' => 'برنامهٔ تمرینی',
                'secondary_cta_url' => '/schedule',
            ],
            [
                'eyebrow' => 'افتخارآفرینی',
                'title' => 'قهرمانان کازرون بر سکوی کشور',
                'subtitle' => 'سه مدال طلا در مسابقات قهرمانی استان فارس',
                'description' => 'جودوکاران شهرستان در دورهٔ اخیر بهترین نتیجهٔ پنج سال گذشته را رقم زدند.',
                'cta_label' => 'مشاهدهٔ قهرمانان',
                'cta_url' => '/athletes',
                'secondary_cta_label' => 'آخرین اخبار',
                'secondary_cta_url' => '/news',
            ],
            [
                'eyebrow' => 'ویژهٔ بانوان',
                'title' => 'کلاس‌های اختصاصی جودوی بانوان',
                'subtitle' => 'با مربی خانم و در سالن کاملاً اختصاصی',
                'description' => 'آموزش فنون جودو و دفاع شخصی در محیطی امن و حرفه‌ای برای بانوان شهرستان.',
                'cta_label' => 'اطلاعات کلاس‌ها',
                'cta_url' => '/schedule',
                'secondary_cta_label' => 'تماس با ما',
                'secondary_cta_url' => '/contact',
            ],
            [
                'eyebrow' => 'ردهٔ کودکان',
                'title' => 'جودو، مدرسهٔ ادب و انضباط',
                'subtitle' => 'آموزش بازی‌محور برای کودکان ۶ تا ۹ سال',
                'description' => 'کودک شما پیش از هر فنی، افتادن ایمن، احترام و اعتمادبه‌نفس را می‌آموزد.',
                'cta_label' => 'ثبت‌نام کودکان',
                'cta_url' => '/register',
                'secondary_cta_label' => 'معرفی مربیان',
                'secondary_cta_url' => '/coaches',
            ],
        ];

        foreach ($slides as $i => $slide) {
            Slider::updateOrCreate(
                ['title' => $slide['title']],
                [...$slide,
                    'image' => MediaPlaceholder::scene('hero-'.($i + 1), 1920, 1080),
                    'is_active' => true,
                    'order' => $i,
                ],
            );
        }
    }

    /** پیام‌های تماس و مشترکان خبرنامه — تا صندوق ورودی پنل مدیریت خالی نباشد */
    private function inbox(): void
    {
        $messages = [
            ['رضا کاویانی', 'reza.kaviani@example.com', '09171112200', 'شرایط ثبت‌نام کودک ۷ ساله', 'سلام و وقت بخیر. فرزند من هفت سال دارد و می‌خواستم بدانم برای شرکت در کلاس پایهٔ کودکان چه مدارکی لازم است و آیا ظرفیت خالی وجود دارد؟ سپاسگزارم.', true],
            ['مریم اسدی', 'maryam.asadi@example.com', '09171112201', 'کلاس بانوان در روزهای پنج‌شنبه', 'با سلام، آیا امکان برگزاری کلاس بانوان در روزهای پنج‌شنبه صبح وجود دارد؟ به دلیل شرایط کاری امکان حضور در روزهای دیگر برایم فراهم نیست.', false],
            ['حسین پورمند', null, '09171112202', 'درخواست همکاری به‌عنوان داور', 'سلام. من دارای کارت داوری درجه سه هستم و مایل به همکاری با کمیتهٔ داوران هیئت می‌باشم. لطفاً راهنمایی بفرمایید.', false],
            ['شرکت پخش زاگرس', 'info@zagros.example.com', '07142200000', 'پیشنهاد حمایت مالی', 'با سلام و احترام، شرکت ما علاقه‌مند به حمایت مالی از مسابقات پیش روی هیئت است. خواهشمند است شرایط اسپانسرینگ را اعلام فرمایید.', false],
            ['سمانه رادمهر', 'samane@example.com', '09171112203', 'گواهی ارتقای کمربند', 'سلام، من در آزمون دورهٔ قبل شرکت کردم اما هنوز گواهی کمربند سبز را دریافت نکرده‌ام. پیگیری بفرمایید. متشکرم.', true],
        ];

        foreach ($messages as $i => [$name, $email, $phone, $subject, $body, $read]) {
            ContactMessage::updateOrCreate(
                ['subject' => $subject, 'name' => $name],
                [
                    'email' => $email,
                    'phone' => $phone,
                    'message' => $body,
                    'is_read' => $read,
                    'replied_at' => $read ? now()->subDays($i + 1) : null,
                    'ip_address' => '10.0.0.'.(10 + $i),
                    'created_at' => now()->subDays($i * 2 + 1),
                ],
            );
        }

        foreach ([
            'sara.mohammadi@example.com',
            'ali.hosseini@example.com',
            'narges.k@example.com',
            'mehdi.jokar@example.com',
            'fatemeh.n@example.com',
        ] as $i => $email) {
            NewsletterSubscriber::updateOrCreate(
                ['email' => $email],
                ['confirmed_at' => now()->subDays($i * 3 + 2)],
            );
        }
    }

    private function sponsors(): void
    {
        $sponsors = [
            ['ادارهٔ ورزش و جوانان کازرون', 'platinum'],
            ['شهرداری کازرون', 'platinum'],
            ['بانک ملی ایران', 'gold'],
            ['شرکت صنایع غذایی زاگرس', 'gold'],
            ['بیمهٔ ایران — نمایندگی کازرون', 'partner'],
            ['فروشگاه ورزشی پهلوان', 'partner'],
            ['کلینیک فیزیوتراپی سلامت', 'partner'],
        ];

        foreach ($sponsors as $i => [$name, $tier]) {
            Sponsor::updateOrCreate(
                ['name' => $name],
                [
                    'logo' => MediaPlaceholder::logo($name),
                    'url' => null,
                    'tier' => $tier,
                    'is_active' => true,
                    'order' => $i,
                ],
            );
        }
    }
}
