<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeds the whole site with realistic Persian judo content.
     *
     * Deliberately does NOT use WithoutModelEvents: Enrollment generates its
     * tracking reference and NewsletterSubscriber its unsubscribe token from
     * `creating` hooks, and both columns are non-null and unique.
     *
     * Order matters — people before training (classes need coaches), training
     * before venues (the hall board mirrors the class sessions), content before
     * media (albums link to events), and enrollments last of all.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            PeopleSeeder::class,
            TrainingSeeder::class,
            VenueSeeder::class,
            ContentSeeder::class,
            MediaSeeder::class,
            SiteSeeder::class,
            EnrollmentSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('حساب‌های نمونه — ورود فقط با شمارهٔ موبایل و کد پیامکی');
        $this->command->table(
            ['نقش', 'موبایل', 'مقصد پس از ورود'],
            [
                ['مدیر', '09171234567', '/admin'],
                ['مربی', '09171112233', '/coach'],
                ['ورزشکار', '09173334455', '/dashboard'],
            ],
        );
        $this->command->line('  با SMS_GATEWAY=log کد ورود در همان صفحه نشان داده می‌شود.');
    }
}
