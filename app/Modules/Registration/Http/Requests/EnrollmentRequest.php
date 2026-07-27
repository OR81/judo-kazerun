<?php

declare(strict_types=1);

namespace App\Modules\Registration\Http\Requests;

use App\Enums\DocumentType;
use App\Enums\Gender;
use App\Models\TrainingClass;
use App\Support\PersianNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class EnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Persian and Arabic-Indic digits are folded to Latin before any rule runs. */
    protected function prepareForValidation(): void
    {
        $this->merge(collect(['national_code', 'mobile', 'emergency_phone', 'birth_date'])
            ->filter(fn (string $field) => $this->filled($field))
            ->mapWithKeys(fn (string $field) => [$field => PersianNumber::toLatin($this->input($field))])
            ->all());
    }

    public function rules(): array
    {
        $documents = [];

        foreach (DocumentType::cases() as $type) {
            $documents["documents.{$type->value}"] = [
                $type->isRequired() ? 'required' : 'nullable',
                'file',
                'mimes:'.implode(',', $type->acceptedMimes()),
                'max:'.$type->maxKilobytes(),
            ];
        }

        return [
            'training_class_id' => [
                'required',
                Rule::exists('training_classes', 'id')->where('is_active', true),
            ],

            'first_name' => ['required', 'string', 'min:2', 'max:60'],
            'last_name' => ['required', 'string', 'min:2', 'max:60'],

            // Mirrors the unique index on (training_class_id, national_code) so a
            // repeat application shows a readable message instead of a 500.
            'national_code' => [
                'required',
                'digits:10',
                Rule::unique('enrollments', 'national_code')
                    ->where('training_class_id', $this->input('training_class_id')),
            ],
            'mobile' => ['required', 'regex:/^09\d{9}$/'],
            'email' => ['nullable', 'email:rfc', 'max:180'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(Gender::values())],

            'guardian_name' => ['nullable', 'string', 'max:120'],
            'emergency_phone' => ['required', 'regex:/^0\d{9,10}$/'],
            'address' => ['nullable', 'string', 'max:400'],
            'medical_notes' => ['nullable', 'string', 'max:600'],

            'terms' => ['accepted'],

            ...$documents,
        ];
    }

    /**
     * Rules that need more than one field, or a database lookup.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $class = TrainingClass::find($this->input('training_class_id'));

                if (! $class) {
                    return;
                }

                if ($class->is_full) {
                    $validator->errors()->add('training_class_id', 'ظرفیت این کلاس تکمیل شده است. لطفاً کلاس دیگری انتخاب کنید.');
                }

                // A women-only class shouldn't accept a male applicant, and vice versa.
                if ($class->gender !== Gender::Mixed && $this->input('gender') !== $class->gender->value) {
                    $validator->errors()->add('gender', "این کلاس ویژهٔ {$class->gender->label()} است.");
                }

                // Under-18 applicants need a guardian on file.
                $birthDate = $this->date('birth_date');

                if ($birthDate && $birthDate->age < 18 && blank($this->input('guardian_name'))) {
                    $validator->errors()->add('guardian_name', 'برای متقاضیان زیر ۱۸ سال، درج نام ولی الزامی است.');
                }

                if ($birthDate && ! $this->ageFitsGroup($birthDate->age, $class)) {
                    $validator->errors()->add(
                        'birth_date',
                        "سن متقاضی با ردهٔ «{$class->age_group->label()}» ({$class->age_group->ageRange()}) هم‌خوانی ندارد.",
                    );
                }
            },
        ];
    }

    private function ageFitsGroup(int $age, TrainingClass $class): bool
    {
        // A one-year tolerance on each side; the board approves edge cases manually.
        return match ($class->age_group->value) {
            'kids' => $age >= 5 && $age <= 10,
            'juniors' => $age >= 9 && $age <= 14,
            'cadets' => $age >= 13 && $age <= 18,
            'adults' => $age >= 17,
            'veterans' => $age >= 34,
            default => true,
        };
    }

    public function attributes(): array
    {
        $labels = [
            'training_class_id' => 'کلاس انتخابی',
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'national_code' => 'کد ملی',
            'mobile' => 'شمارهٔ موبایل',
            'email' => 'رایانامه',
            'birth_date' => 'تاریخ تولد',
            'gender' => 'جنسیت',
            'guardian_name' => 'نام ولی',
            'emergency_phone' => 'تلفن اضطراری',
            'address' => 'نشانی',
            'medical_notes' => 'توضیحات پزشکی',
            'terms' => 'قوانین و مقررات',
        ];

        foreach (DocumentType::cases() as $type) {
            $labels["documents.{$type->value}"] = $type->label();
        }

        return $labels;
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'شمارهٔ موبایل را به شکل ۰۹۱۲۳۴۵۶۷۸۹ وارد کنید.',
            'emergency_phone.regex' => 'شمارهٔ تماس اضطراری معتبر نیست.',
            'national_code.digits' => 'کد ملی باید دقیقاً ۱۰ رقم باشد.',
            'national_code.unique' => 'با این کد ملی قبلاً در همین کلاس ثبت‌نام شده است. برای پیگیری با دفتر هیئت تماس بگیرید.',
            'terms.accepted' => 'برای ادامه باید قوانین و مقررات را بپذیرید.',
        ];
    }
}
