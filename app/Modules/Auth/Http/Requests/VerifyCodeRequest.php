<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Requests;

use App\Support\PersianNumber;
use Illuminate\Foundation\Http\FormRequest;

/** گام دوم ورود — بررسی کد یک‌بارمصرف */
class VerifyCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** An Iranian keyboard types ۱۲۳۴۵۶; the stored code is Latin. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => preg_replace('/\D+/', '', PersianNumber::toLatin((string) $this->input('code'))),
        ]);
    }

    public function rules(): array
    {
        $length = (int) config('sms.code.length', 6);

        return [
            'code' => ['required', 'string', 'digits:'.$length],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'کد ورود را وارد کنید.',
            'code.digits' => 'کد ورود '.fa((string) config('sms.code.length', 6)).' رقم است.',
        ];
    }

    public function code(): string
    {
        return (string) $this->validated()['code'];
    }
}
