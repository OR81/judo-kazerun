<?php

declare(strict_types=1);

namespace App\Modules\Registration\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\TransactionStatus;
use App\Models\Enrollment;
use App\Models\TrainingClass;
use App\Models\Transaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    /**
     * Create an enrollment, store its documents, and open a pending transaction.
     *
     * Wrapped in a transaction so a failed upload can't leave a half-registered
     * applicant behind.
     *
     * @param  array<string, UploadedFile|null>  $documents
     */
    public function create(array $data, array $documents, ?int $userId = null): Enrollment
    {
        return DB::transaction(function () use ($data, $documents, $userId) {
            /** @var TrainingClass $class */
            $class = TrainingClass::query()
                ->whereKey($data['training_class_id'])
                // Hold the row so two simultaneous applicants can't both take the last seat.
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($class->is_full, 422, 'ظرفیت این کلاس تکمیل شده است.');

            $enrollment = Enrollment::create([
                'user_id' => $userId,
                'training_class_id' => $class->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'national_code' => $data['national_code'],
                'mobile' => $data['mobile'],
                'email' => $data['email'] ?? null,
                'birth_date' => $data['birth_date'],
                'gender' => $data['gender'],
                'guardian_name' => $data['guardian_name'] ?? null,
                'emergency_phone' => $data['emergency_phone'] ?? null,
                'address' => $data['address'] ?? null,
                'medical_notes' => $data['medical_notes'] ?? null,
                'amount' => $class->monthly_fee,
                'status' => $class->monthly_fee > 0
                    ? EnrollmentStatus::AwaitingPayment
                    : EnrollmentStatus::Pending,
            ]);

            foreach ($documents as $type => $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $enrollment->documents()->create([
                    'type' => $type,
                    'path' => $file->store("enrollments/{$enrollment->reference}", 'public'),
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ]);
            }

            return $enrollment;
        });
    }

    /** Open a pending transaction for an enrollment that owes money. */
    public function openTransaction(Enrollment $enrollment): Transaction
    {
        return $enrollment->transactions()->create([
            'gateway' => config('payment.default'),
            'amount' => $enrollment->amount,
            'status' => TransactionStatus::Pending,
        ]);
    }

    /**
     * Settle a paid transaction and take a seat in the class.
     *
     * Idempotent: gateways retry callbacks and visitors refresh the return
     * page, and neither may double-count the seat.
     */
    public function markPaid(Transaction $transaction, string $refId, ?string $cardPan, array $payload = []): void
    {
        if ($transaction->status === TransactionStatus::Paid) {
            return;
        }

        DB::transaction(function () use ($transaction, $refId, $cardPan, $payload) {
            $transaction->update([
                'status' => TransactionStatus::Paid,
                'ref_id' => $refId,
                'card_pan' => $cardPan,
                'payload' => $payload,
                'paid_at' => now(),
            ]);

            $enrollment = $transaction->enrollment;
            $enrollment->update(['status' => EnrollmentStatus::Paid]);

            $enrollment->trainingClass()->incrementEach(['enrolled_count' => 1]);
        });
    }

    public function markFailed(Transaction $transaction, string $message, array $payload = []): void
    {
        if ($transaction->status === TransactionStatus::Paid) {
            return;
        }

        $transaction->update([
            'status' => TransactionStatus::Failed,
            'message' => $message,
            'payload' => $payload,
        ]);
    }
}
