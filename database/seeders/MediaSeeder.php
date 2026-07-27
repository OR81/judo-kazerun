<?php

namespace Database\Seeders;

use App\Enums\DownloadCategory;
use App\Enums\GalleryType;
use App\Models\Download;
use App\Models\Event;
use App\Models\GalleryAlbum;
use App\Support\MediaPlaceholder;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $this->albums();
        $this->downloads();
    }

    private function albums(): void
    {
        $albums = [
            ['مسابقات قهرمانی استان فارس', GalleryType::Photo, 14, 3, true],
            ['اردوی آماده‌سازی تیم منتخب', GalleryType::Photo, 10, 16, false],
            ['افتتاح سالن جدید خانهٔ جودو', GalleryType::Photo, 8, 21, true],
            ['تمرینات ردهٔ کودکان و نونهالان', GalleryType::Photo, 12, 30, false],
            ['کلاس‌های جودوی بانوان', GalleryType::Photo, 9, 38, false],
            ['ویدئوهای آموزشی فنون پایه', GalleryType::Video, 5, 45, true],
        ];

        foreach ($albums as $i => [$title, $type, $count, $daysAgo, $featured]) {
            $slug = fa_slug($title);

            $album = GalleryAlbum::updateOrCreate(
                ['slug' => $slug],
                [
                    'event_id' => $i === 0 ? Event::where('status', 'completed')->value('id') : null,
                    'title' => $title,
                    'description' => "مجموعه تصاویر مربوط به {$title} در هیئت جودو شهرستان کازرون.",
                    'cover_image' => MediaPlaceholder::scene('album-'.$slug, 1200, 800),
                    'type' => $type,
                    'taken_on' => now()->subDays($daysAgo),
                    'is_featured' => $featured,
                    'is_active' => true,
                    'order' => $i,
                ],
            );

            $album->items()->delete();

            for ($n = 1; $n <= $count; $n++) {
                // Mixed portrait/landscape so the masonry column layout has rhythm.
                $portrait = $n % 3 === 0;
                $width = $portrait ? 800 : 1200;
                $height = $portrait ? 1100 : 800;

                $album->items()->create([
                    'type' => $type,
                    'path' => MediaPlaceholder::scene("{$slug}-{$n}", $width, $height),
                    'thumbnail' => MediaPlaceholder::scene("{$slug}-{$n}", 600, (int) round(600 * $height / $width)),
                    'caption' => "{$title} — تصویر ".fa($n),
                    'width' => $width,
                    'height' => $height,
                    'order' => $n,
                ]);
            }
        }
    }

    private function downloads(): void
    {
        $files = [
            [DownloadCategory::Form, 'فرم ثبت‌نام کلاس‌های آموزشی', 'فرم رسمی ثبت‌نام برای متقاضیانی که به‌صورت حضوری اقدام می‌کنند.', 'pdf', 245_000],
            [DownloadCategory::Form, 'فرم رضایت‌نامهٔ والدین', 'ویژهٔ ورزشکاران زیر هجده سال؛ تکمیل و امضای ولی الزامی است.', 'pdf', 180_000],
            [DownloadCategory::Form, 'فرم درخواست شرکت در آزمون دان', 'فرم مخصوص داوطلبان آزمون ارتقای دان یک تا سه.', 'docx', 96_000],
            [DownloadCategory::Form, 'فرم معرفی‌نامه به هیئت استان', 'برای اعزام ورزشکار به مسابقات برون‌شهری تکمیل می‌شود.', 'docx', 78_000],
            [DownloadCategory::Regulation, 'آیین‌نامهٔ انضباطی هیئت جودو کازرون', 'مقررات حضور در تمرینات، پوشش و رفتار ورزشی در تاتامی.', 'pdf', 512_000],
            [DownloadCategory::Regulation, 'مقررات فنی مسابقات شهرستان', 'شرایط شرکت، اوزان رسمی و نحوهٔ برگزاری مسابقات شهرستانی.', 'pdf', 640_000],
            [DownloadCategory::Regulation, 'قوانین به‌روزشدهٔ داوری IJF', 'ترجمهٔ فارسی آخرین تغییرات قوانین فدراسیون بین‌المللی جودو.', 'pdf', 1_280_000],
            [DownloadCategory::Educational, 'جزوهٔ فنون پایهٔ کمربند زرد تا سبز', 'راهنمای تصویری فنون لازم برای ارتقای کمربند در ردهٔ کیو.', 'pdf', 3_400_000],
            [DownloadCategory::Educational, 'راهنمای اجرای ناگه‌نوکاتا', 'شرح گام‌به‌گام پانزده فن ناگه‌نوکاتا ویژهٔ داوطلبان دان.', 'pdf', 2_750_000],
            [DownloadCategory::Educational, 'برنامهٔ آمادگی جسمانی ویژهٔ جودوکاران', 'برنامهٔ تمرینی هشت‌هفته‌ای تدوین‌شده توسط کادر فنی هیئت.', 'pdf', 890_000],
        ];

        foreach ($files as $i => [$category, $title, $description, $extension, $size]) {
            $slug = fa_slug($title);

            Download::updateOrCreate(
                ['slug' => $slug],
                [
                    'category' => $category,
                    'title' => $title,
                    'description' => $description,
                    // Seed data has no real binaries; the controller returns a
                    // clear message when a file is missing rather than a 500.
                    'file_path' => "downloads/{$slug}.{$extension}",
                    'file_name' => "{$title}.{$extension}",
                    'extension' => $extension,
                    'size' => $size,
                    'downloads_count' => random_int(15, 480),
                    'is_active' => true,
                    'order' => $i,
                ],
            );
        }
    }
}
