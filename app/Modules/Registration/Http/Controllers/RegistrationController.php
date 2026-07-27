<?php

declare(strict_types=1);

namespace App\Modules\Registration\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\EnrollmentStatus;
use App\Enums\Gender;
use App\Models\Enrollment;
use App\Models\TrainingClass;
use App\Models\Transaction;
use App\Modules\Payment\Contracts\PaymentGateway;
use App\Modules\Registration\Http\Requests\EnrollmentRequest;
use App\Modules\Registration\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollments,
        private readonly PaymentGateway $gateway,
    ) {}

    public function create(Request $request): View
    {
        $classes = TrainingClass::query()
            ->active()
            ->with(['coach', 'sessions'])
            ->ordered()
            ->get();

        return view('pages.register.create', [
            'classes' => $classes,
            // Deep link from the schedule page pre-selects a class.
            'selected' => $classes->firstWhere('slug', $request->string('class')->toString()),
            'documentTypes' => DocumentType::cases(),
            'genders' => Gender::options(),
        ]);
    }

    public function store(EnrollmentRequest $request): RedirectResponse
    {
        $enrollment = $this->enrollments->create(
            data: $request->validated(),
            documents: $request->file('documents', []),
            userId: $request->user()?->id,
        );

        // Free classes (the invited squad) skip payment entirely.
        if ($enrollment->amount <= 0) {
            return redirect()
                ->route('registration.success', $enrollment)
                ->with('success', 'ثبت‌نام شما با موفقیت انجام شد.');
        }

        $transaction = $this->enrollments->openTransaction($enrollment);

        $result = $this->gateway->request(
            $transaction,
            route('registration.callback', $transaction),
        );

        if (! $result->successful) {
            $this->enrollments->markFailed($transaction, $result->message, $result->payload);

            return redirect()
                ->route('registration.success', $enrollment)
                ->with('error', $result->message);
        }

        return redirect()->away($result->redirectUrl);
    }

    /**
     * Where the gateway sends the visitor back to.
     *
     * Safe to hit more than once — markPaid() is idempotent.
     */
    public function callback(Request $request, Transaction $transaction): RedirectResponse
    {
        $result = $this->gateway->verify($transaction, $request->query());

        if ($result->successful) {
            $this->enrollments->markPaid(
                $transaction,
                $result->referenceId ?? '',
                $result->cardPan,
                $result->payload,
            );

            return redirect()
                ->route('registration.success', $transaction->enrollment)
                ->with('success', $result->message);
        }

        $this->enrollments->markFailed($transaction, $result->message, $result->payload);

        return redirect()
            ->route('registration.success', $transaction->enrollment)
            ->with('error', $result->message);
    }

    public function success(Enrollment $enrollment): View
    {
        return view('pages.register.success', [
            'enrollment' => $enrollment->load(['trainingClass.coach', 'trainingClass.sessions', 'transactions', 'documents']),
            'transaction' => $enrollment->transactions()->latest()->first(),
            'isPaid' => in_array($enrollment->status, [EnrollmentStatus::Paid, EnrollmentStatus::Approved], true),
        ]);
    }

    /**
     * Stand-in for a bank's confirm/cancel screen, used by the `fake` driver so
     * the flow can be walked end to end without a merchant account.
     */
    public function sandbox(Request $request, Transaction $transaction): View
    {
        abort_unless(config('payment.default') === 'fake', 404);

        return view('pages.register.sandbox', [
            'transaction' => $transaction->load('enrollment.trainingClass'),
            'callback' => $request->string('callback')->toString(),
        ]);
    }
}
