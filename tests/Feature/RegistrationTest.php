<?php

use App\Enums\EnrollmentStatus;
use App\Enums\Gender;
use App\Enums\TransactionStatus;
use App\Models\Enrollment;
use App\Models\TrainingClass;
use App\Models\Transaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedAll();
    Storage::fake('public');
    config()->set('payment.default', 'fake');
});

/** A valid application for the given class. */
function application(TrainingClass $class, array $overrides = []): array
{
    return [
        'training_class_id' => $class->id,
        'first_name' => 'کیان',
        'last_name' => 'مرادی',
        'national_code' => '2281234567',
        'mobile' => '09171239876',
        'birth_date' => now()->subYears(20)->format('Y-m-d'),
        'gender' => Gender::Male->value,
        'emergency_phone' => '09171112233',
        'address' => 'کازرون، خیابان نمونه',
        'terms' => '1',
        'documents' => [
            'national_card' => UploadedFile::fake()->image('card.jpg'),
            'photo' => UploadedFile::fake()->image('photo.jpg'),
            'medical_certificate' => UploadedFile::fake()->image('medical.jpg'),
        ],
        ...$overrides,
    ];
}

/** An open adults class that accepts men. */
function openAdultClass(): TrainingClass
{
    return TrainingClass::query()
        ->where('age_group', 'adults')
        ->whereIn('gender', ['male', 'mixed'])
        ->where('monthly_fee', '>', 0)
        ->get()
        ->first(fn (TrainingClass $c) => ! $c->is_full);
}

it('shows the registration wizard', function () {
    $this->get('/register')->assertOk()->assertSee('انتخاب کلاس');
});

it('pre-selects a class passed in the query string', function () {
    $class = openAdultClass();

    $this->get('/register?class='.$class->slug)
        ->assertOk()
        ->assertSee($class->title);
});

it('walks a full registration through to a paid receipt', function () {
    $class = openAdultClass();
    $seatsBefore = $class->enrolled_count;

    // 1. Submit the application.
    $response = $this->post('/register', application($class));

    $enrollment = Enrollment::where('national_code', '2281234567')->firstOrFail();

    expect($enrollment->status)->toBe(EnrollmentStatus::AwaitingPayment)
        ->and($enrollment->documents)->toHaveCount(3)
        ->and($enrollment->reference)->toStartWith('KJ-');

    // Documents actually landed on the disk.
    foreach ($enrollment->documents as $document) {
        Storage::disk('public')->assertExists($document->path);
    }

    // 2. The fake gateway redirects to the sandbox page.
    $transaction = $enrollment->transactions()->firstOrFail();
    expect($transaction->status)->toBe(TransactionStatus::Pending);
    $response->assertRedirect();

    // 3. Confirm payment through the callback.
    $this->get(route('registration.callback', $transaction).'?status=OK')
        ->assertRedirect(route('registration.success', $enrollment))
        ->assertSessionHas('success');

    // 4. Everything settled, and a seat was taken.
    expect($transaction->fresh()->status)->toBe(TransactionStatus::Paid)
        ->and($enrollment->fresh()->status)->toBe(EnrollmentStatus::Paid)
        ->and($class->fresh()->enrolled_count)->toBe($seatsBefore + 1);

    $this->get(route('registration.success', $enrollment))
        ->assertOk()
        ->assertSee($enrollment->reference);
});

it('does not double-count a seat when the callback is replayed', function () {
    $class = openAdultClass();
    $this->post('/register', application($class));

    $enrollment = Enrollment::where('national_code', '2281234567')->firstOrFail();
    $transaction = $enrollment->transactions()->firstOrFail();

    $this->get(route('registration.callback', $transaction).'?status=OK');
    $seatsAfterFirst = $class->fresh()->enrolled_count;

    // Gateways retry callbacks and visitors refresh the return page.
    $this->get(route('registration.callback', $transaction).'?status=OK');

    expect($class->fresh()->enrolled_count)->toBe($seatsAfterFirst);
});

it('records a cancelled payment as failed and leaves the seat free', function () {
    $class = openAdultClass();
    $seatsBefore = $class->enrolled_count;

    $this->post('/register', application($class));

    $enrollment = Enrollment::where('national_code', '2281234567')->firstOrFail();
    $transaction = $enrollment->transactions()->firstOrFail();

    $this->get(route('registration.callback', $transaction).'?status=NOK')
        ->assertSessionHas('error');

    expect($transaction->fresh()->status)->toBe(TransactionStatus::Failed)
        ->and($class->fresh()->enrolled_count)->toBe($seatsBefore);
});

it('skips payment for a free class', function () {
    $class = TrainingClass::query()->where('monthly_fee', 0)->firstOrFail();

    $this->post('/register', application($class, [
        'national_code' => '2289999999',
        'gender' => Gender::Male->value,
    ]))->assertSessionHasNoErrors();

    $enrollment = Enrollment::where('national_code', '2289999999')->firstOrFail();

    expect($enrollment->status)->toBe(EnrollmentStatus::Pending)
        ->and($enrollment->transactions)->toHaveCount(0);
});

it('validates required fields', function () {
    $this->post('/register', [])->assertSessionHasErrors([
        'training_class_id', 'first_name', 'last_name',
        'national_code', 'mobile', 'birth_date', 'gender', 'terms',
    ]);
});

it('folds Persian digits before validating', function () {
    $class = openAdultClass();

    $this->post('/register', application($class, [
        'national_code' => '۲۲۸۱۲۳۴۵۶۷',
        'mobile' => '۰۹۱۷۱۲۳۹۸۷۶',
    ]))->assertSessionHasNoErrors();

    expect(Enrollment::where('national_code', '2281234567')->exists())->toBeTrue();
});

it('rejects a male applicant for a women-only class', function () {
    $class = TrainingClass::query()->where('gender', 'female')->firstOrFail();

    $this->post('/register', application($class))->assertSessionHasErrors('gender');
});

it('rejects an applicant whose age does not fit the class', function () {
    $kids = TrainingClass::query()->where('age_group', 'kids')->firstOrFail();

    $this->post('/register', application($kids, [
        'birth_date' => now()->subYears(30)->format('Y-m-d'),
    ]))->assertSessionHasErrors('birth_date');
});

it('requires a guardian for applicants under eighteen', function () {
    $cadets = TrainingClass::query()->where('age_group', 'cadets')->firstOrFail();

    $this->post('/register', application($cadets, [
        'birth_date' => now()->subYears(15)->format('Y-m-d'),
        'guardian_name' => '',
    ]))->assertSessionHasErrors('guardian_name');
});

it('refuses a second registration for the same person in one class', function () {
    $class = openAdultClass();

    $this->post('/register', application($class))->assertSessionHasNoErrors();

    // Must surface as a readable validation error, not a unique-constraint 500.
    $this->post('/register', application($class))
        ->assertSessionHasErrors('national_code');

    expect(Enrollment::where('national_code', '2281234567')->count())->toBe(1);
});

it('rejects an oversized document', function () {
    $class = openAdultClass();

    $this->post('/register', application($class, [
        'documents' => [
            'national_card' => UploadedFile::fake()->create('card.jpg', 5000, 'image/jpeg'),
            'photo' => UploadedFile::fake()->image('photo.jpg'),
            'medical_certificate' => UploadedFile::fake()->image('medical.jpg'),
        ],
    ]))->assertSessionHasErrors('documents.national_card');
});

it('hides the sandbox page when a real gateway is configured', function () {
    $transaction = Transaction::query()->firstOrFail();

    config()->set('payment.default', 'zarinpal');

    $this->get(route('registration.sandbox', $transaction))->assertNotFound();
});
