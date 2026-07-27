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
     * Order matters — people before training (classes need coaches), content
     * before media (albums link to events), and enrollments last of all.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            PeopleSeeder::class,
            TrainingSeeder::class,
            ContentSeeder::class,
            MediaSeeder::class,
            SiteSeeder::class,
            EnrollmentSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('حساب‌های نمونه (رمز عبور همه: password)');
        $this->command->table(
            ['نقش', 'موبایل', 'ایمیل'],
            [
                ['مدیر', '09171234567', 'admin@judo-kazerun.ir'],
                ['مربی', '09171112233', 'coach@judo-kazerun.ir'],
                ['ورزشکار', '09173334455', 'athlete@judo-kazerun.ir'],
            ],
        );
    }
}
