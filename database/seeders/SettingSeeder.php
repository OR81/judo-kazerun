<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // هویت
            'general' => [
                'site_title' => 'هیئت جودو شهرستان کازرون',
                'site_tagline' => 'پرورش قهرمانان، ترویج اخلاق پهلوانی',
                'site_description' => 'مرجع رسمی جودوی شهرستان کازرون؛ برنامهٔ تمرین، ثبت‌نام آنلاین، اخبار مسابقات و معرفی مربیان و قهرمانان.',
                'founded_year' => '1372',
                'federation' => 'فدراسیون جودوی جمهوری اسلامی ایران',
                'province_board' => 'هیئت جودو استان فارس',
            ],

            // تماس
            'contact' => [
                'address' => 'فارس، کازرون، بلوار شهید بهشتی، مجموعهٔ ورزشی تختی، خانهٔ جودو',
                'postal_code' => '7331916554',
                'phone' => '07142234567',
                'fax' => '07142234568',
                'mobile' => '09171234567',
                'whatsapp' => '989171234567',
                'email' => 'info@judo-kazerun.ir',
                'support_email' => 'support@judo-kazerun.ir',
                // کازرون
                'map_lat' => '29.6193',
                'map_lng' => '51.6543',
                'map_zoom' => '15',
            ],

            'hours' => [
                'hours_weekdays' => 'شنبه تا چهارشنبه: ۹:۰۰ تا ۱۳:۰۰ و ۱۶:۰۰ تا ۲۱:۰۰',
                'hours_thursday' => 'پنج‌شنبه: ۹:۰۰ تا ۱۳:۰۰',
                'hours_friday' => 'جمعه: تعطیل',
                'hours_note' => 'ساعات کار دفتر هیئت. زمان تمرین کلاس‌ها در صفحهٔ برنامهٔ تمرینی اعلام شده است.',
            ],

            /*
             * خانهٔ جودو — the building itself.
             *
             * Times are stored as plain H:i so the home page can decide, on the
             * server, whether the hall is open right now; the office hours above
             * are separate and describe the desk, not the mat.
             */
            'hall' => [
                'hall_name' => 'خانهٔ جودو کازرون',
                'hall_intro' => 'خانهٔ جودو کازرون سالن اختصاصی هیئت است: دو سالن تاتامی، رختکن و دوش، '
                    .'و برنامه‌ای که از هشت صبح تا یازده شب روی مات ادامه دارد. کلاس‌های هیئت اینجا برگزار '
                    .'می‌شود و سانس‌های خالی به باشگاه‌ها، مدارس و گروه‌های ورزشی اجاره داده می‌شود.',
                'hall_open_from' => '08:00',
                'hall_open_to' => '23:00',
                'hall_friday_open_from' => '08:00',
                'hall_friday_open_to' => '13:00',
                'hall_rent_note' => 'اجارهٔ سانس با هماهنگی دفتر هیئت و عقد قرارداد کوتاه‌مدت انجام می‌شود. '
                    .'برای رزرو ثابت هفتگی، تخفیف دوره‌ای در نظر گرفته شده است.',
                'hall_monthly_discount' => '۱۵',
                'hall_min_booking' => '۴',
            ],

            'social' => [
                'instagram' => 'https://instagram.com/judo.kazerun',
                'telegram' => 'https://t.me/judo_kazerun',
                'aparat' => 'https://aparat.com/judo.kazerun',
                'youtube' => '',
                'x' => '',
            ],

            // آمار صفحهٔ اصلی
            'stats' => [
                'stat_athletes' => '۴۸۰',
                'stat_coaches' => '۱۴',
                'stat_medals' => '۱۲۷',
                'stat_clubs' => '۹',
            ],

            'about' => [
                'about_short' => 'هیئت جودو شهرستان کازرون از سال ۱۳۷۲ با هدف گسترش ورزش جودو، شناسایی و پرورش استعدادهای جوان و ترویج اخلاق پهلوانی در سطح شهرستان فعالیت می‌کند.',
                'about_mission' => 'مأموریت ما تربیت جودوکارانی است که در کنار توانمندی فنی، به ادب، احترام و انضباط آراسته باشند؛ چرا که جودو پیش از آن‌که ورزش باشد، راه و روش زندگی است.',
            ],
        ];

        foreach ($settings as $group => $pairs) {
            foreach ($pairs as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group' => $group],
                );
            }
        }
    }
}
