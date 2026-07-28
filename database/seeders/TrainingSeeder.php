<?php

namespace Database\Seeders;

use App\Enums\AgeGroup;
use App\Enums\ClassLevel;
use App\Enums\Gender;
use App\Models\Coach;
use App\Models\TrainingClass;
use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
{
    public function run(): void
    {
        $coaches = Coach::orderBy('order')->get()->keyBy('slug');
        $coachAt = fn (int $i) => $coaches->values()->get($i % max(1, $coaches->count()))?->id;

        // day_of_week: 0 = شنبه … 6 = جمعه
        $classes = [
            [
                'title' => 'کلاس پایهٔ کودکان',
                'age_group' => AgeGroup::Kids,
                'gender' => Gender::Mixed,
                'level' => ClassLevel::Beginner,
                'capacity' => 24,
                'enrolled' => 19,
                'fee' => 350_000,
                'venue' => 'سالن اصلی خانهٔ جودو',
                'coach' => 4,
                'description' => 'آشنایی با اصول اولیهٔ جودو، فن افتادن ایمن (اوکه‌می)، تعادل و بازی‌های حرکتی. مناسب برای کودکانی که برای نخستین بار روی تاتامی می‌آیند.',
                'sessions' => [[0, '16:30', '17:45'], [2, '16:30', '17:45']],
            ],
            [
                'title' => 'کلاس نونهالان',
                'age_group' => AgeGroup::Juniors,
                'gender' => Gender::Mixed,
                'level' => ClassLevel::Beginner,
                'capacity' => 24,
                'enrolled' => 22,
                'fee' => 380_000,
                'venue' => 'سالن اصلی خانهٔ جودو',
                'coach' => 4,
                'description' => 'آموزش فنون پایهٔ پرتابی و کار روی زمین، به‌همراه تمرینات آمادگی جسمانی متناسب با سن.',
                'sessions' => [[0, '18:00', '19:15'], [2, '18:00', '19:15'], [4, '18:00', '19:15']],
            ],
            [
                'title' => 'کلاس نوجوانان',
                'age_group' => AgeGroup::Cadets,
                'gender' => Gender::Male,
                'level' => ClassLevel::Intermediate,
                'capacity' => 20,
                'enrolled' => 20,
                'fee' => 420_000,
                'venue' => 'سالن اصلی خانهٔ جودو',
                'coach' => 2,
                'description' => 'تکمیل فنون پرتابی، تمرین رندوری و آماده‌سازی برای مسابقات ردهٔ نوجوانان استان.',
                'sessions' => [[1, '17:30', '19:00'], [3, '17:30', '19:00'], [5, '09:00', '10:30']],
            ],
            [
                'title' => 'کلاس بزرگسالان — مقدماتی',
                'age_group' => AgeGroup::Adults,
                'gender' => Gender::Male,
                'level' => ClassLevel::Beginner,
                'capacity' => 25,
                'enrolled' => 14,
                'fee' => 450_000,
                'venue' => 'سالن اصلی خانهٔ جودو',
                'coach' => 1,
                'description' => 'ویژهٔ بزرگسالانی که تازه جودو را آغاز کرده‌اند؛ آموزش گام‌به‌گام فنون پایه و آمادگی جسمانی عمومی.',
                'sessions' => [[1, '19:30', '21:00'], [3, '19:30', '21:00']],
            ],
            [
                'title' => 'کلاس بزرگسالان — پیشرفته',
                'age_group' => AgeGroup::Adults,
                'gender' => Gender::Male,
                'level' => ClassLevel::Advanced,
                'capacity' => 20,
                'enrolled' => 17,
                'fee' => 480_000,
                'venue' => 'سالن اصلی خانهٔ جودو',
                'coach' => 1,
                'description' => 'تمرین تخصصی رندوری، تحلیل فنی و آماده‌سازی مسابقات قهرمانی استان و کشور.',
                'sessions' => [[0, '19:30', '21:15'], [2, '19:30', '21:15'], [4, '19:30', '21:15']],
            ],
            [
                'title' => 'کلاس بانوان — مقدماتی',
                'age_group' => AgeGroup::Adults,
                'gender' => Gender::Female,
                'level' => ClassLevel::Beginner,
                'capacity' => 22,
                'enrolled' => 16,
                'fee' => 420_000,
                'venue' => 'سالن شمارهٔ ۲ — تاتامی تمرین',
                'coach' => 3,
                'description' => 'کلاس ویژهٔ بانوان با مربی خانم؛ آموزش فنون پایهٔ جودو و دفاع شخصی در محیطی کاملاً اختصاصی.',
                'sessions' => [[1, '16:00', '17:30'], [3, '16:00', '17:30']],
            ],
            [
                'title' => 'کلاس بانوان — پیشرفته',
                'age_group' => AgeGroup::Adults,
                'gender' => Gender::Female,
                'level' => ClassLevel::Advanced,
                'capacity' => 18,
                'enrolled' => 11,
                'fee' => 450_000,
                'venue' => 'سالن شمارهٔ ۲ — تاتامی تمرین',
                'coach' => 3,
                'description' => 'تمرین تخصصی بانوان با تمرکز بر کاتا، رندوری و آماده‌سازی مسابقات قهرمانی بانوان.',
                'sessions' => [[0, '16:00', '17:30'], [2, '16:00', '17:30']],
            ],
            [
                'title' => 'تیم منتخب شهرستان',
                'age_group' => AgeGroup::Adults,
                'gender' => Gender::Mixed,
                'level' => ClassLevel::Elite,
                'capacity' => 16,
                'enrolled' => 13,
                'fee' => 0,
                'venue' => 'سالن اصلی خانهٔ جودو',
                'coach' => 5,
                'description' => 'تمرین تیم منتخب کازرون برای حضور در مسابقات برون‌شهری. عضویت تنها با دعوت کادر فنی و بدون شهریه است.',
                'sessions' => [[5, '17:00', '19:00'], [6, '09:00', '11:00']],
            ],
            [
                'title' => 'کلاس پیشکسوتان',
                'age_group' => AgeGroup::Veterans,
                'gender' => Gender::Mixed,
                'level' => ClassLevel::Intermediate,
                'capacity' => 20,
                'enrolled' => 8,
                'fee' => 300_000,
                'venue' => 'سالن شمارهٔ ۲ — تاتامی تمرین',
                'coach' => 0,
                'description' => 'تمرین سبک و تفریحی ویژهٔ پیشکسوتان جودو با تمرکز بر حفظ آمادگی و مرور فنون.',
                'sessions' => [[4, '10:00', '11:30']],
            ],
        ];

        foreach ($classes as $i => $data) {
            $class = TrainingClass::updateOrCreate(
                ['slug' => fa_slug($data['title'])],
                [
                    'coach_id' => $coachAt($data['coach']),
                    'title' => $data['title'],
                    'age_group' => $data['age_group'],
                    'gender' => $data['gender'],
                    'level' => $data['level'],
                    'capacity' => $data['capacity'],
                    'enrolled_count' => $data['enrolled'],
                    'monthly_fee' => $data['fee'],
                    'venue' => $data['venue'],
                    'description' => $data['description'],
                    'is_active' => true,
                    'order' => $i,
                ],
            );

            $class->sessions()->delete();

            foreach ($data['sessions'] as [$day, $start, $end]) {
                $class->sessions()->create([
                    'day_of_week' => $day,
                    'start_time' => $start,
                    'end_time' => $end,
                    'venue' => $data['venue'],
                ]);
            }
        }
    }
}
