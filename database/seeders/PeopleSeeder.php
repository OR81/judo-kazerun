<?php

namespace Database\Seeders;

use App\Enums\BoardPosition;
use App\Enums\CompetitionLevel;
use App\Enums\Gender;
use App\Enums\MedalRank;
use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\Athlete;
use App\Models\Belt;
use App\Models\BoardMember;
use App\Models\Coach;
use App\Models\User;
use App\Support\MediaPlaceholder;
use Illuminate\Database\Seeder;

class PeopleSeeder extends Seeder
{
    public function run(): void
    {
        $this->belts();
        $this->accounts();
        $this->coaches();
        $this->board();
        $this->athletes();
    }

    /** کمربندها — ترتیب رسمی جودو */
    private function belts(): void
    {
        $belts = [
            ['سفید', 'white', '#F8FAFC', null],
            ['زرد', 'yellow', '#FACC15', null],
            ['نارنجی', 'orange', '#FB923C', null],
            ['سبز', 'green', '#22C55E', null],
            ['آبی', 'blue', '#3B82F6', null],
            ['قهوه‌ای', 'brown', '#92400E', null],
            ['مشکی', 'black', '#111827', null],
            ['مشکی دان ۱', 'dan-1', '#111827', 1],
            ['مشکی دان ۲', 'dan-2', '#111827', 2],
            ['مشکی دان ۳', 'dan-3', '#111827', 3],
            ['مشکی دان ۴', 'dan-4', '#111827', 4],
            ['مشکی دان ۵', 'dan-5', '#111827', 5],
        ];

        foreach ($belts as $i => [$name, $slug, $color, $dan]) {
            Belt::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'color' => $color, 'dan_level' => $dan, 'order' => $i],
            );
        }
    }

    /** حساب‌های نمونه برای سه پرتال */
    private function accounts(): void
    {
        User::updateOrCreate(
            ['mobile' => '09171234567'],
            [
                'name' => 'مدیر سامانه',
                'role' => UserRole::Admin,
                'national_code' => '2280000001',
                'gender' => Gender::Male,
                'city' => 'کازرون',
                'is_active' => true,
            ],
        );
    }

    private function coaches(): void
    {
        $coaches = [
            [
                'name' => 'محمدرضا دهقانی',
                'title' => 'سرمربی هیئت',
                'dan' => 5,
                'years' => 22,
                'summary' => 'سرمربی هیئت جودو کازرون و مدرس رسمی فدراسیون؛ پرورش‌دهندهٔ بیش از سی مدال‌آور استانی و کشوری.',
                'bio' => 'محمدرضا دهقانی از سال ۱۳۷۷ به‌طور حرفه‌ای وارد جودو شد و پس از کسب دان ۵ و گذراندن دوره‌های مربیگری درجه یک فدراسیون، سرمربیگری هیئت جودو کازرون را بر عهده گرفت. تمرکز او بر آموزش پایه‌های صحیح فنی و پرورش روحیهٔ پهلوانی در نوجوانان است. شاگردان او تاکنون در مسابقات قهرمانی کشور، لیگ نوجوانان و جام‌های بین‌المللی به مدال رسیده‌اند.',
                'specialties' => ['اوچی‌ماتا', 'سئوی‌ناگه', 'کار روی زمین (نه‌وازا)'],
                'certificates' => ['مربیگری درجه یک فدراسیون جودو', 'داوری ملی درجه ۲', 'دورهٔ بین‌المللی کوچینگ IJF', 'مدرسی رسمی کاتا'],
                'featured' => true,
            ],
            [
                'name' => 'علی‌اکبر فرهمند',
                'title' => 'مربی بزرگسالان',
                'dan' => 4,
                'years' => 17,
                'summary' => 'مربی تیم بزرگسالان و مسئول کمیتهٔ فنی؛ متخصص آماده‌سازی مسابقات قهرمانی.',
                'bio' => 'علی‌اکبر فرهمند سابقهٔ حضور در تیم منتخب استان فارس را دارد و پس از خداحافظی از میدان مسابقه، به مربیگری روی آورد. او هدایت تیم بزرگسالان هیئت را بر عهده دارد و برنامه‌های آماده‌سازی بدنی و تاکتیکی تیم را طراحی می‌کند.',
                'specialties' => ['هاراگوشی', 'آماده‌سازی جسمانی', 'تحلیل حریف'],
                'certificates' => ['مربیگری درجه ۲ فدراسیون', 'دورهٔ آمادگی جسمانی ویژهٔ جودو', 'کمک‌های اولیهٔ ورزشی'],
                'featured' => true,
            ],
            [
                'name' => 'سعید کاظمی',
                'title' => 'مربی نوجوانان',
                'dan' => 3,
                'years' => 12,
                'summary' => 'مربی ردهٔ نوجوانان و مسئول شناسایی استعدادها در مدارس شهرستان.',
                'bio' => 'سعید کاظمی طرح استعدادیابی جودو در مدارس کازرون را پایه‌گذاری کرد. او معتقد است ورود صحیح به جودو در سنین پایین، مهم‌ترین سرمایه‌گذاری برای آیندهٔ این رشته در شهرستان است.',
                'specialties' => ['استعدادیابی', 'اوسوتو‌گاری', 'آموزش پایه'],
                'certificates' => ['مربیگری درجه ۲ فدراسیون', 'دورهٔ روان‌شناسی ورزشی کودکان'],
                'featured' => true,
            ],
            [
                'name' => 'مریم صادقی',
                'title' => 'مربی بانوان',
                'dan' => 3,
                'years' => 11,
                'summary' => 'مربی و مسئول کمیتهٔ بانوان هیئت؛ بنیان‌گذار کلاس‌های جودوی بانوان در کازرون.',
                'bio' => 'مریم صادقی نخستین مربی بانوان جودو در شهرستان کازرون است. او از سال ۱۳۹۳ کلاس‌های ویژهٔ بانوان را راه‌اندازی کرد و امروز بیش از هشتاد ورزشکار زن زیر نظر او تمرین می‌کنند. شاگردان او در مسابقات قهرمانی بانوان استان فارس صاحب مدال شده‌اند.',
                'specialties' => ['کاتا', 'دفاع شخصی', 'جودوی بانوان'],
                'certificates' => ['مربیگری درجه ۲ فدراسیون', 'داوری استانی', 'دورهٔ تخصصی کاتا'],
                'featured' => true,
            ],
            [
                'name' => 'حمید نوروزی',
                'title' => 'مربی کودکان',
                'dan' => 2,
                'years' => 8,
                'summary' => 'مربی ردهٔ کودکان با رویکرد بازی‌محور در آموزش مهارت‌های پایه.',
                'bio' => 'حمید نوروزی آموزش جودو به کودکان را با روش بازی‌محور دنبال می‌کند؛ روشی که در آن کودک پیش از آموختن فن، با تعادل، افتادن ایمن و احترام به حریف آشنا می‌شود.',
                'specialties' => ['اوکه‌می (فن افتادن)', 'بازی‌های حرکتی', 'تعادل و هماهنگی'],
                'certificates' => ['مربیگری درجه ۳ فدراسیون', 'دورهٔ آموزش ورزش به کودکان'],
                'featured' => false,
            ],
            [
                'name' => 'بهرام رستمی',
                'title' => 'مربی تیم منتخب',
                'dan' => 4,
                'years' => 15,
                'summary' => 'مربی تیم منتخب شهرستان و مربی بدن‌ساز؛ مسئول اردوهای آماده‌سازی.',
                'bio' => 'بهرام رستمی هدایت تیم منتخب کازرون در مسابقات برون‌شهری را بر عهده دارد و برنامهٔ اردوهای آماده‌سازی پیش از مسابقات استانی و کشوری را تنظیم می‌کند.',
                'specialties' => ['تای‌اوتوشی', 'بدن‌سازی تخصصی', 'مدیریت اردو'],
                'certificates' => ['مربیگری درجه ۲ فدراسیون', 'دورهٔ بدن‌سازی تخصصی ورزش‌های رزمی'],
                'featured' => false,
            ],
        ];

        foreach ($coaches as $i => $data) {
            $belt = Belt::where('slug', 'dan-'.$data['dan'])->first();
            $slug = fa_slug($data['name']);

            Coach::updateOrCreate(
                ['slug' => $slug],
                [
                    'belt_id' => $belt?->id,
                    'name' => $data['name'],
                    'title' => $data['title'],
                    'photo' => MediaPlaceholder::portrait('coach-'.$data['name']),
                    'dan_rank' => $data['dan'],
                    'summary' => $data['summary'],
                    'bio' => $data['bio'],
                    'experience_years' => $data['years'],
                    'specialties' => $data['specialties'],
                    'certificates' => $data['certificates'],
                    'phone' => '0917'.random_int(1000000, 9999999),
                    'email' => $slug.'@judo-kazerun.ir',
                    'instagram' => 'judo.kazerun',
                    'is_featured' => $data['featured'],
                    'is_active' => true,
                    'order' => $i,
                ],
            );
        }

        // The head coach gets a portal login so the coach dashboard has real data.
        $head = Coach::where('slug', fa_slug('محمدرضا دهقانی'))->first()
            ?? Coach::orderBy('order')->first();

        if ($head) {
            $user = User::updateOrCreate(
                ['mobile' => '09171112233'],
                [
                    'name' => $head->name,
                    'role' => UserRole::Coach,
                    'national_code' => '2280000002',
                    'gender' => Gender::Male,
                    'city' => 'کازرون',
                    'is_active' => true,
                ],
            );

            $head->update(['user_id' => $user->id]);
        }
    }

    private function board(): void
    {
        $members = [
            ['جواد امیری', BoardPosition::President, null, 'رئیس هیئت جودو شهرستان کازرون و عضو شورای فنی هیئت استان فارس.'],
            ['فاطمه کریمی', BoardPosition::VicePresident, null, 'نایب‌رئیس هیئت و مسئول هماهنگی امور بانوان با هیئت استان.'],
            ['محسن زارعی', BoardPosition::Secretary, null, 'دبیر هیئت و مسئول پیگیری مکاتبات اداری و ثبت‌نام مسابقات.'],
            ['اکبر شریفی', BoardPosition::Treasurer, null, 'خزانه‌دار هیئت و مسئول تنظیم بودجهٔ سالانه و گزارش‌های مالی.'],
            ['ناصر بهمنی', BoardPosition::CommitteeHead, 'کمیتهٔ فنی', 'مسئول تدوین برنامهٔ فنی و نظارت بر کیفیت تمرینات باشگاه‌ها.'],
            ['رضا موسوی', BoardPosition::CommitteeHead, 'کمیتهٔ داوران', 'مسئول ساماندهی داوران شهرستان و برگزاری دوره‌های بازآموزی.'],
            ['سمیه احمدی', BoardPosition::CommitteeHead, 'کمیتهٔ بانوان', 'مسئول توسعهٔ جودوی بانوان و هماهنگی مسابقات ویژهٔ بانوان.'],
            ['کامران یوسفی', BoardPosition::CommitteeHead, 'کمیتهٔ آموزش', 'مسئول برگزاری دوره‌های مربیگری و آزمون‌های ارتقای کمربند.'],
            ['دکتر شهرام نیک‌پور', BoardPosition::CommitteeHead, 'کمیتهٔ پزشکی', 'مسئول نظارت بر سلامت ورزشکاران و پیشگیری از آسیب‌های ورزشی.'],
            ['هادی قنبری', BoardPosition::Member, 'کمیتهٔ فنی', 'عضو کمیتهٔ فنی و ناظر مسابقات ردهٔ نوجوانان.'],
        ];

        foreach ($members as $i => [$name, $position, $committee, $summary]) {
            BoardMember::updateOrCreate(
                ['slug' => fa_slug($name)],
                [
                    'name' => $name,
                    'position' => $position,
                    'committee' => $committee,
                    'photo' => MediaPlaceholder::portrait('board-'.$name),
                    'summary' => $summary,
                    'bio' => $summary.' ایشان از اعضای فعال هیئت جودو کازرون هستند و در تدوین برنامهٔ راهبردی توسعهٔ جودوی شهرستان مشارکت دارند.',
                    'email' => 'board@judo-kazerun.ir',
                    'is_active' => true,
                    'order' => $i,
                ],
            );
        }
    }

    private function athletes(): void
    {
        $athletes = [
            ['امیرحسین رضایی', 'male', '۸۱- کیلوگرم', 'dan-2', true, true, [
                ['طلا', MedalRank::Gold, CompetitionLevel::National, 'مسابقات قهرمانی کشور — ردهٔ جوانان', 1403],
                ['نقره', MedalRank::Silver, CompetitionLevel::National, 'لیگ برتر جودو باشگاه‌های کشور', 1402],
                ['طلا', MedalRank::Gold, CompetitionLevel::Provincial, 'قهرمانی استان فارس', 1402],
            ]],
            ['مهدی جوکار', 'male', '۷۳- کیلوگرم', 'dan-1', true, true, [
                ['برنز', MedalRank::Bronze, CompetitionLevel::International, 'جام بین‌المللی فجر', 1403],
                ['طلا', MedalRank::Gold, CompetitionLevel::Provincial, 'قهرمانی استان فارس', 1403],
            ]],
            ['زهرا نعمتی', 'female', '۵۷- کیلوگرم', 'dan-1', true, true, [
                ['طلا', MedalRank::Gold, CompetitionLevel::National, 'قهرمانی بانوان کشور', 1403],
                ['نقره', MedalRank::Silver, CompetitionLevel::Provincial, 'قهرمانی بانوان استان فارس', 1402],
            ]],
            ['علی صابری', 'male', '۶۶- کیلوگرم', 'black', false, true, [
                ['طلا', MedalRank::Gold, CompetitionLevel::Provincial, 'قهرمانی نوجوانان استان فارس', 1403],
                ['برنز', MedalRank::Bronze, CompetitionLevel::National, 'مسابقات نوجوانان کشور', 1403],
            ]],
            ['نگار پاکدل', 'female', '۶۳- کیلوگرم', 'brown', false, true, [
                ['طلا', MedalRank::Gold, CompetitionLevel::Provincial, 'قهرمانی بانوان استان فارس', 1403],
            ]],
            ['محمد شفیعی', 'male', '۹۰- کیلوگرم', 'black', false, false, [
                ['نقره', MedalRank::Silver, CompetitionLevel::Provincial, 'قهرمانی بزرگسالان استان فارس', 1403],
                ['برنز', MedalRank::Bronze, CompetitionLevel::City, 'جام رمضان کازرون', 1403],
            ]],
            ['ابوالفضل کریمی', 'male', '۶۰- کیلوگرم', 'brown', false, false, [
                ['طلا', MedalRank::Gold, CompetitionLevel::City, 'جام رمضان کازرون', 1403],
            ]],
            ['یاسمن رحیمی', 'female', '۵۲- کیلوگرم', 'green', false, false, [
                ['برنز', MedalRank::Bronze, CompetitionLevel::Provincial, 'قهرمانی نونهالان استان فارس', 1403],
            ]],
            ['سجاد بهرامی', 'male', '۱۰۰- کیلوگرم', 'blue', false, false, [
                ['نقره', MedalRank::Silver, CompetitionLevel::City, 'جام رمضان کازرون', 1403],
            ]],
        ];

        $coaches = Coach::orderBy('order')->get();

        foreach ($athletes as $i => [$name, $gender, $weight, $beltSlug, $national, $featured, $medals]) {
            $athlete = Athlete::updateOrCreate(
                ['slug' => fa_slug($name)],
                [
                    'belt_id' => Belt::where('slug', $beltSlug)->value('id'),
                    'coach_id' => $coaches[$i % $coaches->count()]->id ?? null,
                    'name' => $name,
                    'photo' => MediaPlaceholder::portrait('athlete-'.$name),
                    'birth_date' => now()->subYears(random_int(16, 27))->subDays(random_int(0, 364)),
                    'gender' => $gender,
                    'weight_class' => $weight,
                    'club' => 'خانهٔ جودو کازرون',
                    'city' => 'کازرون',
                    'bio' => "{$name} از ورزشکاران هیئت جودو کازرون در وزن {$weight} است و زیر نظر کادر فنی هیئت تمرین می‌کند.",
                    'is_national_team' => $national,
                    'is_featured' => $featured,
                    'is_active' => true,
                    'order' => $i,
                ],
            );

            $athlete->achievements()->delete();

            foreach ($medals as [$title, $rank, $level, $competition, $year]) {
                Achievement::create([
                    'athlete_id' => $athlete->id,
                    'title' => 'مدال '.$title,
                    'competition' => $competition,
                    'rank' => $rank,
                    'level' => $level,
                    'year' => $year,
                ]);
            }
        }

        // Give the first athlete a portal login for the athlete dashboard.
        $first = Athlete::orderBy('order')->first();

        if ($first) {
            $user = User::updateOrCreate(
                ['mobile' => '09173334455'],
                [
                    'name' => $first->name,
                    'role' => UserRole::Athlete,
                    'national_code' => '2280000003',
                    'birth_date' => $first->birth_date,
                    'gender' => $first->gender,
                    'city' => 'کازرون',
                    'is_active' => true,
                ],
            );

            $first->update(['user_id' => $user->id]);
        }
    }
}
