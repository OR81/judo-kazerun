<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\SlotStatus;
use App\Models\TrainingClass;
use App\Models\Venue;
use App\Support\MediaPlaceholder;
use Illuminate\Database\Seeder;

/**
 * خانهٔ جودو و سانس‌های آن.
 *
 * The board's own classes are not seeded twice: their slots are derived from the
 * training_sessions rows already in place, so the hall board and the weekly
 * timetable can never disagree. Rentals and free slots are then layered around
 * them, and anything that would overlap an existing slot in the same hall is
 * skipped — the hall physically cannot be in two states at once.
 *
 * Runs after TrainingSeeder for exactly that reason.
 */
class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $halls = $this->seedVenues();

        Venue::query()->with('slots')->get()->each(fn (Venue $v) => $v->slots()->delete());

        $this->seedClassSlots($halls);
        $this->seedRentals($halls);
    }

    /** @return array<string, Venue> */
    private function seedVenues(): array
    {
        $venues = [
            [
                'name' => 'سالن اصلی خانهٔ جودو',
                'tagline' => 'تاتامی استاندارد مسابقه',
                'description' => 'سالن اصلی خانهٔ جودو کازرون با تاتامی مورد تأیید فدراسیون، جایگاه تماشاگر و رختکن مجزا؛ '
                    .'محل برگزاری کلاس‌های هیئت، مسابقات شهرستانی و سانس‌های اجاره‌ای باشگاه‌ها و گروه‌های ورزشی.',
                'tatami_area' => 200,
                'capacity' => 40,
                'session_rate' => 450_000,
                'features' => [
                    'تاتامی استاندارد IJF',
                    'جایگاه تماشاگر ۱۲۰ نفره',
                    'رختکن مجزای بانوان و آقایان',
                    'دوش آب گرم',
                    'سیستم گرمایش و تهویهٔ مطبوع',
                    'روشنایی LED ورزشی',
                    'ترازوی دیجیتال و تابلوی امتیاز',
                    'جعبهٔ کمک‌های اولیه و برانکارد',
                ],
                'order' => 0,
            ],
            [
                'name' => 'سالن شمارهٔ ۲ — تاتامی تمرین',
                'tagline' => 'سالن مجزا با ورودی مستقل',
                'description' => 'سالن کوچک‌تر خانهٔ جودو با ورودی و رختکن مستقل. چون کاملاً از سالن اصلی جدا است، '
                    .'سانس‌های بانوان، کلاس پیشکسوتان و گروه‌های کم‌جمعیت اینجا برگزار می‌شوند و در ساعات خالی '
                    .'برای تمرین آمادگی جسمانی اجاره داده می‌شود.',
                'tatami_area' => 96,
                'capacity' => 20,
                'session_rate' => 260_000,
                'features' => [
                    'تاتامی تمرینی',
                    'ورودی و رختکن مستقل',
                    'مناسب سانس اختصاصی بانوان',
                    'آینهٔ سرتاسری دیواری',
                    'وزنه و تجهیزات آمادگی جسمانی',
                    'دوش آب گرم',
                ],
                'order' => 1,
            ],
        ];

        $saved = [];

        foreach ($venues as $data) {
            $venue = Venue::updateOrCreate(
                ['slug' => fa_slug($data['name'])],
                [...$data, 'image' => MediaPlaceholder::scene($data['name']), 'is_active' => true],
            );

            $saved[$venue->name] = $venue;
        }

        return $saved;
    }

    /**
     * The board's own classes, mirrored into the hall board from their real
     * weekly sessions.
     *
     * @param  array<string, Venue>  $halls
     */
    private function seedClassSlots(array $halls): void
    {
        $main = reset($halls);

        foreach (TrainingClass::with('sessions')->get() as $class) {
            $venue = $halls[$class->venue] ?? $main;

            foreach ($class->sessions as $session) {
                $venue->slots()->create([
                    'training_class_id' => $class->id,
                    'day_of_week' => $session->day_of_week,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'status' => SlotStatus::BoardClass,
                    'gender' => $class->gender,
                    'holder' => $class->title,
                    'price' => 0,
                ]);
            }
        }
    }

    /**
     * Rented and free slots around the classes. day_of_week: 0 = شنبه … 6 = جمعه
     *
     * @param  array<string, Venue>  $halls
     */
    private function seedRentals(array $halls): void
    {
        [$main, $second] = array_values($halls);

        // [hall, day, start, end, status, gender, holder, note]
        $rows = [
            // ── mornings in the main hall: schools and clubs ───────────────────
            [$main, 0, '08:00', '09:30', SlotStatus::Booked, Gender::Male, 'دبیرستان شهید بهشتی — درس تربیت بدنی', null],
            [$main, 0, '09:45', '11:15', SlotStatus::Open, Gender::Mixed, null, null],
            [$main, 1, '08:00', '09:30', SlotStatus::Booked, Gender::Mixed, 'باشگاه پهلوان کازرون', null],
            [$main, 1, '09:45', '11:15', SlotStatus::Booked, Gender::Female, 'گروه دفاع شخصی بانوان', 'با مربی خانم'],
            [$main, 2, '08:00', '09:30', SlotStatus::Open, Gender::Mixed, null, null],
            [$main, 2, '09:45', '11:15', SlotStatus::Booked, Gender::Male, 'هیئت کشتی شهرستان کازرون', 'تمرین ردهٔ نوجوانان'],
            [$main, 3, '08:00', '09:30', SlotStatus::Booked, Gender::Mixed, 'باشگاه شهدای نودان', null],
            [$main, 3, '09:45', '11:15', SlotStatus::Open, Gender::Mixed, null, null],
            [$main, 4, '08:00', '09:30', SlotStatus::Open, Gender::Mixed, null, null],
            [$main, 5, '08:00', '09:30', SlotStatus::Booked, Gender::Mixed, 'تیم جودو دانشگاه آزاد کازرون', null],
            [$main, 5, '11:00', '12:30', SlotStatus::Open, Gender::Mixed, null, null],
            [$main, 6, '08:00', '09:30', SlotStatus::Open, Gender::Mixed, null, 'سانس ویژهٔ جمعه'],
            [$main, 6, '11:15', '12:45', SlotStatus::Open, Gender::Female, null, 'قابل تبدیل به سانس بانوان'],

            // ── late evenings in the main hall ─────────────────────────────────
            [$main, 0, '21:30', '23:00', SlotStatus::Open, Gender::Male, null, null],
            [$main, 1, '21:15', '22:45', SlotStatus::Booked, Gender::Male, 'باشگاه سما', 'اجارهٔ ماهانه'],
            [$main, 2, '21:30', '23:00', SlotStatus::Open, Gender::Male, null, null],
            [$main, 3, '21:15', '22:45', SlotStatus::Booked, Gender::Male, 'باشگاه سما', 'اجارهٔ ماهانه'],
            [$main, 4, '21:30', '23:00', SlotStatus::Open, Gender::Mixed, null, null],
            [$main, 5, '19:30', '21:00', SlotStatus::Booked, Gender::Mixed, 'هیئت ورزش‌های رزمی شهرستان', null],

            // ── the second hall ────────────────────────────────────────────────
            [$second, 0, '08:30', '10:00', SlotStatus::Open, Gender::Mixed, null, null],
            [$second, 0, '17:45', '19:15', SlotStatus::Booked, Gender::Female, 'گروه آمادگی جسمانی بانوان', null],
            [$second, 1, '08:30', '10:00', SlotStatus::Open, Gender::Mixed, null, null],
            [$second, 1, '18:30', '20:00', SlotStatus::Open, Gender::Male, null, null],
            [$second, 2, '17:45', '19:15', SlotStatus::Booked, Gender::Female, 'گروه آمادگی جسمانی بانوان', null],
            [$second, 2, '19:30', '21:00', SlotStatus::Open, Gender::Mixed, null, null],
            [$second, 3, '08:30', '10:00', SlotStatus::Closed, Gender::Mixed, null, 'سرویس دوره‌ای دستگاه‌ها'],
            [$second, 3, '18:30', '20:00', SlotStatus::Open, Gender::Male, null, null],
            [$second, 4, '17:00', '18:30', SlotStatus::Open, Gender::Mixed, null, null],
            [$second, 5, '10:00', '11:30', SlotStatus::Open, Gender::Mixed, null, null],
            [$second, 6, '09:00', '10:30', SlotStatus::Open, Gender::Mixed, null, null],
        ];

        foreach ($rows as [$venue, $day, $start, $end, $status, $gender, $holder, $note]) {
            if ($this->overlaps($venue, $day, $start, $end)) {
                continue;
            }

            $venue->slots()->create([
                'day_of_week' => $day,
                'start_time' => $start,
                'end_time' => $end,
                'status' => $status,
                'gender' => $gender,
                'holder' => $holder,
                'note' => $note,
            ]);
        }
    }

    /** A hall can only be in one state at a time — half-open interval, so slots may touch. */
    private function overlaps(Venue $venue, int $day, string $start, string $end): bool
    {
        return $venue->slots()
            ->where('day_of_week', $day)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();
    }
}
