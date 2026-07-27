<?php

use App\Models\TrainingSession;
use App\Support\JalaliDate;
use App\Support\PersianNumber;
use Illuminate\Support\Carbon;

describe('PersianNumber', function () {
    it('converts Latin digits to Persian', function () {
        expect(PersianNumber::toPersian('1404'))->toBe('۱۴۰۴')
            ->and(PersianNumber::toPersian(12345))->toBe('۱۲۳۴۵');
    });

    it('folds both Persian and Arabic-Indic digits back to Latin', function () {
        expect(PersianNumber::toLatin('۱۴۰۴'))->toBe('1404')
            // Arabic-Indic ٤ and ٥ differ from Persian ۴ and ۵ and must also fold.
            ->and(PersianNumber::toLatin('٠١٢٣٤٥٦٧٨٩'))->toBe('0123456789');
    });

    it('groups thousands with the Persian separator', function () {
        expect(PersianNumber::format(1250000))->toBe('۱٬۲۵۰٬۰۰۰');
    });

    it('labels weekdays with Saturday first', function () {
        expect(PersianNumber::weekday(0))->toBe('شنبه')
            ->and(PersianNumber::weekday(6))->toBe('جمعه');
    });
});

describe('JalaliDate', function () {
    it('maps Nowruz to 1 Farvardin', function () {
        expect(JalaliDate::long('2026-03-21'))->toBe('۱ فروردین ۱۴۰۵');
    });

    it('formats each style', function () {
        $date = Carbon::parse('2026-07-27 18:30:00');

        expect(JalaliDate::short($date))->toBe('۱۴۰۵/۰۵/۰۵')
            ->and(JalaliDate::long($date))->toBe('۵ مرداد ۱۴۰۵')
            ->and(JalaliDate::year($date))->toBe('۱۴۰۵')
            ->and(JalaliDate::month($date))->toBe('مرداد')
            ->and(JalaliDate::day($date))->toBe('۵')
            ->and(JalaliDate::time($date))->toBe('۱۸:۳۰');
    });

    it('returns a dash for null rather than throwing', function () {
        expect(JalaliDate::long(null))->toBe('—')
            ->and(JalaliDate::short(''))->toBe('—');
    });

    it('emits a machine-readable Gregorian value for <time datetime>', function () {
        expect(JalaliDate::machine('2026-07-27'))->toStartWith('2026-07-27');
    });
});

describe('helpers', function () {
    it('does not lose to the global jdate() shipped by morilog/jalali', function () {
        // morilog registers jdate() and its vendor autoload wins, so ours is
        // named shamsi(). Guard against a future rename reintroducing the clash.
        expect(shamsi('2026-03-21'))->toBe('۱ فروردین ۱۴۰۵')
            ->and(function_exists('shamsi'))->toBeTrue();
    });

    it('keeps Persian characters in slugs', function () {
        expect(fa_slug('محمدرضا دهقانی'))->toBe('محمدرضا-دهقانی');
    });

    it('formats Toman amounts', function () {
        expect(toman(450000))->toBe('۴۵۰٬۰۰۰ تومان');
    });
});

describe('weekday mapping', function () {
    it('maps Carbon Saturday onto index 0', function () {
        // Carbon: 0 = Sunday … 6 = Saturday. Ours: 0 = شنبه.
        expect(TrainingSession::fromCarbonDay(6))->toBe(0)
            ->and(TrainingSession::fromCarbonDay(0))->toBe(1)
            ->and(TrainingSession::fromCarbonDay(5))->toBe(6);
    });
});
