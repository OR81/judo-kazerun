<?php

/*
|--------------------------------------------------------------------------
| Site navigation
|--------------------------------------------------------------------------
|
| One source of truth for the desktop mega menu, the mobile drawer and the
| footer, so the three can never drift apart.
|
| `route`   — a named route; the active state is matched against its pattern.
| `columns` — turns the item into a mega-menu panel.
| `feature` — optional promo card rendered at the end of a mega panel.
|
*/

return [
    [
        'label' => 'خانه',
        'route' => 'home',
        'icon' => 'fa-house',
    ],

    [
        'label' => 'آموزش و ثبت‌نام',
        'icon' => 'fa-graduation-cap',
        'active' => ['schedule*', 'register*', 'coaches*'],
        'columns' => [
            [
                'heading' => 'تمرین',
                'links' => [
                    ['label' => 'برنامهٔ تمرینی هفتگی', 'route' => 'schedule', 'icon' => 'fa-calendar-days', 'description' => 'جدول کلاس‌ها به تفکیک روز و ردهٔ سنی'],
                    ['label' => 'ثبت‌نام آنلاین', 'route' => 'register', 'icon' => 'fa-user-plus', 'description' => 'ثبت‌نام و پرداخت شهریه به‌صورت اینترنتی'],
                ],
            ],
            [
                'heading' => 'کادر فنی',
                'links' => [
                    ['label' => 'مربیان هیئت', 'route' => 'coaches.index', 'icon' => 'fa-chalkboard-user', 'description' => 'معرفی مربیان، درجهٔ دان و سوابق'],
                    ['label' => 'فرم‌ها و جزوات', 'route' => 'downloads', 'icon' => 'fa-file-arrow-down', 'description' => 'آیین‌نامه‌ها و منابع آموزشی'],
                ],
            ],
        ],
        'feature' => [
            'title' => 'ثبت‌نام دورهٔ جدید آغاز شد',
            'description' => 'ظرفیت کلاس‌ها محدود است؛ همین حالا جای خود را رزرو کنید.',
            'route' => 'register',
            'cta' => 'شروع ثبت‌نام',
        ],
    ],

    [
        'label' => 'هیئت',
        'icon' => 'fa-shield-halved',
        'active' => ['board*', 'athletes*'],
        'columns' => [
            [
                'heading' => 'ساختار',
                'links' => [
                    ['label' => 'هیئت‌رئیسه و کمیته‌ها', 'route' => 'board', 'icon' => 'fa-sitemap', 'description' => 'رئیس، نایب‌رئیس، دبیر، خزانه‌دار و کمیته‌ها'],
                    ['label' => 'تماس با ما', 'route' => 'contact', 'icon' => 'fa-location-dot', 'description' => 'نشانی، تلفن و ساعات کاری دفتر'],
                ],
            ],
            [
                'heading' => 'ورزشکاران',
                'links' => [
                    ['label' => 'قهرمانان و مدال‌آوران', 'route' => 'athletes.index', 'icon' => 'fa-medal', 'description' => 'افتخارات جودوکاران شهرستان'],
                    ['label' => 'ملی‌پوشان', 'route' => 'athletes.national', 'icon' => 'fa-flag', 'description' => 'نمایندگان کازرون در تیم ملی'],
                ],
            ],
        ],
    ],

    [
        'label' => 'اخبار و رسانه',
        'icon' => 'fa-newspaper',
        'active' => ['news*', 'events*', 'gallery*'],
        'columns' => [
            [
                'heading' => 'اخبار',
                'links' => [
                    ['label' => 'آخرین اخبار', 'route' => 'news.index', 'icon' => 'fa-newspaper', 'description' => 'گزارش‌ها، اطلاعیه‌ها و مطالب آموزشی'],
                    ['label' => 'تقویم رویدادها', 'route' => 'events.index', 'icon' => 'fa-calendar-check', 'description' => 'مسابقات، آزمون دان و اردوها'],
                ],
            ],
            [
                'heading' => 'رسانه',
                'links' => [
                    ['label' => 'گالری تصاویر', 'route' => 'gallery', 'icon' => 'fa-images', 'description' => 'آلبوم مسابقات و تمرینات'],
                    ['label' => 'ویدئوهای آموزشی', 'route' => 'gallery.videos', 'icon' => 'fa-video', 'description' => 'آموزش فنون پایه و گزارش‌های تصویری'],
                ],
            ],
        ],
    ],

    [
        'label' => 'تماس با ما',
        'route' => 'contact',
        'icon' => 'fa-headset',
    ],
];
