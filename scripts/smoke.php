<?php

use App\Enums\AgeGroup;
use App\Enums\BoardPosition;
use App\Enums\ClassLevel;
use App\Enums\CompetitionLevel;
use App\Enums\DocumentType;
use App\Enums\DownloadCategory;
use App\Enums\EnrollmentStatus;
use App\Enums\EventType;
use App\Enums\GalleryType;
use App\Enums\Gender;
use App\Enums\MedalRank;
use App\Enums\TransactionStatus;
use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\Athlete;
use App\Models\Belt;
use App\Models\BoardMember;
use App\Models\Coach;
use App\Models\ContactMessage;
use App\Models\Download;
use App\Models\Enrollment;
use App\Models\EnrollmentDocument;
use App\Models\Event;
use App\Models\GalleryAlbum;
use App\Models\GalleryItem;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsletterSubscriber;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\Sponsor;
use App\Models\TrainingClass;
use App\Models\TrainingSession;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

/**
 * Boots the framework and touches every model so structural mistakes
 * (bad casts, wrong table names, missing relations) surface immediately.
 *
 *   php scripts/smoke.php
 */
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$models = [
    User::class,
    Belt::class,
    Coach::class,
    Athlete::class,
    Achievement::class,
    BoardMember::class,
    NewsCategory::class,
    News::class,
    Event::class,
    TrainingClass::class,
    TrainingSession::class,
    Enrollment::class,
    EnrollmentDocument::class,
    Transaction::class,
    GalleryAlbum::class,
    GalleryItem::class,
    Download::class,
    Slider::class,
    Sponsor::class,
    Setting::class,
    ContactMessage::class,
    NewsletterSubscriber::class,
];

$failed = 0;

foreach ($models as $class) {
    try {
        $model = new $class;
        $count = $class::query()->count();
        printf("  OK   %-24s %-26s rows=%d\n", class_basename($class), $model->getTable(), $count);
    } catch (Throwable $e) {
        $failed++;
        printf("  FAIL %-24s %s\n", class_basename($class), $e->getMessage());
    }
}

echo "\n--- enums ---\n";
foreach ([
    UserRole::class,
    AgeGroup::class,
    Gender::class,
    EventType::class,
    EnrollmentStatus::class,
    TransactionStatus::class,
    MedalRank::class,
    CompetitionLevel::class,
    BoardPosition::class,
    DownloadCategory::class,
    DocumentType::class,
    ClassLevel::class,
    GalleryType::class,
] as $enum) {
    try {
        printf("  OK   %-20s %s\n", class_basename($enum), implode(' · ', $enum::options()));
    } catch (Throwable $e) {
        $failed++;
        printf("  FAIL %-20s %s\n", class_basename($enum), $e->getMessage());
    }
}

echo "\n--- weekday mapping (Carbon 0=Sunday to ours 0=Saturday) ---\n";
foreach (range(0, 6) as $carbonDay) {
    $ours = TrainingSession::fromCarbonDay($carbonDay);
    printf("  carbon %d -> ours %d = %s\n", $carbonDay, $ours, weekday_fa($ours));
}

echo $failed ? "\n{$failed} failure(s).\n" : "\nAll models and enums load cleanly.\n";
exit($failed ? 1 : 0);
