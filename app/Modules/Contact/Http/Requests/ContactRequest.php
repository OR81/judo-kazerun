<?php

declare(strict_types=1);

namespace App\Modules\Contact\Http\Requests;

use App\Support\PersianNumber;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Visitors on a Persian keyboard type Persian digits; fold them to Latin
     * before validation so a correct phone number isn't rejected.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('phone')) {
            $this->merge(['phone' => PersianNumber::toLatin($this->input('phone'))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['nullable', 'email:rfc', 'max:180'],
            'phone' => ['nullable', 'string', 'regex:/^0?9\d{9}$/'],
            'subject' => ['required', 'string', 'min:3', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام و نام خانوادگی',
            'email' => 'رایانامه',
            'phone' => 'شمارهٔ تماس',
            'subject' => 'موضوع',
            'message' => 'متن پیام',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'شمارهٔ موبایل را به شکل ۰۹۱۲۳۴۵۶۷۸۹ وارد کنید.',
        ];
    }
}
