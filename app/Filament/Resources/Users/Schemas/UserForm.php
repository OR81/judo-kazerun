<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام و نام خانوادگی')
                    ->required(),

                /*
                 * The account's only credential. Whoever holds this SIM can sign in,
                 * so changing it here hands the account over — hence the warning
                 * rather than a bare field.
                 */
                TextInput::make('mobile')
                    ->label('شمارهٔ موبایل')
                    ->helperText('کد ورود به همین شماره پیامک می‌شود؛ تغییر آن یعنی واگذاری دسترسی حساب.')
                    ->tel()
                    ->required()
                    ->unique(User::class, ignoreRecord: true)
                    ->rule('regex:/^09\d{9}$/')
                    ->validationMessages(['regex' => 'شمارهٔ موبایل معتبر نیست. نمونه: ۰۹۱۲۳۴۵۶۷۸۹']),

                Select::make('role')
                    ->label('نقش')
                    ->options(UserRole::options())
                    ->default(UserRole::Athlete->value)
                    ->required(),

                TextInput::make('national_code')
                    ->label('کد ملی')
                    ->maxLength(10),

                DatePicker::make('birth_date')
                    ->label('تاریخ تولد'),

                Select::make('gender')
                    ->label('جنسیت')
                    ->options(Gender::options()),

                TextInput::make('city')
                    ->label('شهر'),

                FileUpload::make('avatar')
                    ->label('تصویر')
                    ->image()
                    ->directory('avatars'),

                Toggle::make('is_active')
                    ->label('فعال')
                    ->helperText('حساب غیرفعال کد ورود دریافت نمی‌کند.')
                    ->default(true),
            ]);
    }
}
