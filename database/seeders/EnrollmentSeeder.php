<?php

namespace Database\Seeders;

use App\Enums\DocumentType;
use App\Enums\EnrollmentStatus;
use App\Enums\Gender;
use App\Enums\TransactionStatus;
use App\Enums\UserRole;
use App\Models\Enrollment;
use App\Models\TrainingClass;
use App\Models\User;
use App\Support\MediaPlaceholder;
use Illuminate\Database\Seeder;

/**
 * A handful of registrations so the admin queue, the athlete dashboard and the
 * finance figures all have something real to show on a fresh install.
 */
class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = TrainingClass::with('sessions')->orderBy('order')->get();

        if ($classes->isEmpty()) {
            return;
        }

        $athleteUser = User::where('role', UserRole::Athlete)->first();

        $applicants = [
            ['امیرحسین', 'رضایی', '2280145678', '09171234501', Gender::Male, 19, EnrollmentStatus::Approved, 0],
            ['سارا', 'محمودی', '2280145679', '09171234502', Gender::Female, 14, EnrollmentStatus::Approved, 5],
            ['پارسا', 'احمدی', '2280145680', '09171234503', Gender::Male, 8, EnrollmentStatus::Paid, 0],
            ['نیما', 'قاسمی', '2280145681', '09171234504', Gender::Male, 12, EnrollmentStatus::Pending, 1],
            ['هستی', 'رحمانی', '2280145682', '09171234505', Gender::Female, 16, EnrollmentStatus::AwaitingPayment, 6],
            ['محمدطاها', 'بیگی', '2280145683', '09171234506', Gender::Male, 10, EnrollmentStatus::Pending, 1],
            ['کیانا', 'سلطانی', '2280145684', '09171234507', Gender::Female, 22, EnrollmentStatus::Approved, 5],
            ['ارشیا', 'مرادی', '2280145685', '09171234508', Gender::Male, 15, EnrollmentStatus::Rejected, 2],
        ];

        foreach ($applicants as $i => [$first, $last, $code, $mobile, $gender, $age, $status, $classIndex]) {
            $class = $classes[$classIndex % $classes->count()];
            $isMinor = $age < 18;

            $enrollment = Enrollment::updateOrCreate(
                ['training_class_id' => $class->id, 'national_code' => $code],
                [
                    'user_id' => $i === 0 ? $athleteUser?->id : null,
                    'first_name' => $first,
                    'last_name' => $last,
                    'mobile' => $mobile,
                    'email' => null,
                    'birth_date' => now()->subYears($age)->subDays(random_int(0, 360)),
                    'gender' => $gender,
                    'guardian_name' => $isMinor ? 'والد '.$last : null,
                    'emergency_phone' => '0917'.random_int(1000000, 9999999),
                    'address' => 'کازرون، خیابان شهید مطهری، کوچهٔ '.fa(random_int(1, 20)),
                    'medical_notes' => $i === 3 ? 'حساسیت فصلی خفیف — بدون محدودیت تمرینی' : null,
                    'has_insurance' => $status !== EnrollmentStatus::Pending,
                    'amount' => $class->monthly_fee,
                    'status' => $status,
                    'approved_at' => $status === EnrollmentStatus::Approved ? now()->subDays(random_int(1, 20)) : null,
                    'admin_notes' => $status === EnrollmentStatus::Rejected ? 'گواهی سلامت پزشکی ناخوانا بود؛ نیازمند بارگذاری مجدد.' : null,
                    'created_at' => now()->subDays(random_int(1, 30)),
                ],
            );

            $enrollment->documents()->delete();

            foreach ([DocumentType::NationalCard, DocumentType::Photo, DocumentType::MedicalCertificate] as $type) {
                $enrollment->documents()->create([
                    'type' => $type,
                    'path' => MediaPlaceholder::scene("doc-{$code}-{$type->value}", 900, 600),
                    'original_name' => $type->value.'.jpg',
                    'size' => random_int(180_000, 1_600_000),
                    'mime' => 'image/jpeg',
                ]);
            }

            $enrollment->transactions()->delete();

            if (in_array($status, [EnrollmentStatus::Paid, EnrollmentStatus::Approved], true)) {
                $enrollment->transactions()->create([
                    'gateway' => 'zarinpal',
                    'amount' => $enrollment->amount,
                    'authority' => 'A'.str_pad((string) random_int(1, 999999999), 35, '0', STR_PAD_LEFT),
                    'ref_id' => (string) random_int(100000000, 999999999),
                    'card_pan' => '6037-****-****-'.random_int(1000, 9999),
                    'status' => TransactionStatus::Paid,
                    'paid_at' => $enrollment->created_at->addMinutes(random_int(3, 40)),
                ]);
            } elseif ($status === EnrollmentStatus::AwaitingPayment) {
                $enrollment->transactions()->create([
                    'gateway' => 'zarinpal',
                    'amount' => $enrollment->amount,
                    'status' => TransactionStatus::Pending,
                ]);
            }
        }
    }
}
