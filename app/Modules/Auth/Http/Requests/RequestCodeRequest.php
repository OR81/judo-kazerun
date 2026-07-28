<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Requests;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

/** گام یکم ورود — گرفتن شمارهٔ موبایل */
class RequestCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalise before validating, so «۰۹۱۷ ۱۲۳ ۴۵۶۷» and «+989171234567» are
     * judged as the number they are rather than rejected on their punctuation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'mobile' => PhoneNumber::normalize($this->input('mobile')) ?? $this->input('mobile'),
        ]);
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => 'شمارهٔ موبایل را وارد کنید.',
            'mobile.regex' => 'شمارهٔ موبایل معتبر نیست. نمونه: ۰۹۱۲۳۴۵۶۷۸۹',
        ];
    }

    public function mobile(): string
    {
        return (string) $this->validated()['mobile'];
    }
}
