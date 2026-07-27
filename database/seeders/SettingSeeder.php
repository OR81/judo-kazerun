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
