<?php

namespace App\Filament\Widgets;

use App\Enums\EnrollmentStatus;
use App\Enums\TransactionStatus;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Enrollment;
use App\Models\News;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BoardOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pending = Enrollment::query()
            ->whereIn('status', [EnrollmentStatus::Pending->value, EnrollmentStatus::Paid->value])
            ->count();

        $revenue = Transaction::query()
            ->where('status', TransactionStatus::Paid)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        return [
            Stat::make('ثبت‌نام در انتظار بررسی', fa_number($pending))
                ->description($pending > 0 ? 'نیازمند تأیید یا رد' : 'همه بررسی شده‌اند')
                ->descriptionIcon($pending > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($pending > 0 ? 'warning' : 'success'),

            Stat::make('ورزشکاران فعال', fa_number(Athlete::query()->where('is_active', true)->count()))
                ->description(fa_number(Athlete::query()->where('is_national_team', true)->count()).' ملی‌پوش')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('primary'),

            Stat::make('مربیان', fa_number(Coach::query()->where('is_active', true)->count()))
                ->description('کادر فنی هیئت')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('درآمد این ماه', toman($revenue))
                ->description('مجموع تراکنش‌های موفق')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('اخبار منتشرشده', fa_number(News::query()->published()->count()))
                ->description(fa_number(News::query()->whereNull('published_at')->count()).' پیش‌نویس')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('gray'),

            Stat::make('ثبت‌نام تأییدشده', fa_number(
                Enrollment::query()->where('status', EnrollmentStatus::Approved)->count()
            ))
                ->description('هنرجویان فعال')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
