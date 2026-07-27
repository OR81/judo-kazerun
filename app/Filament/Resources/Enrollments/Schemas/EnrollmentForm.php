<?php

namespace App\Filament\Resources\Enrollments\Schemas;

use App\Enums\EnrollmentStatus;
use App\Enums\Gender;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات متقاضی')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')->label('نام')->required()->maxLength(60),
                        TextInput::make('last_name')->label('نام خانوادگی')->required()->maxLength(60),

                        TextInput::make('national_code')
                            ->label('کد ملی')
                            ->required()
                            ->rule('digits:10'),

                        TextInput::make('mobile')
                            ->label('شمارهٔ موبایل')
                            ->required()
                            ->tel()
                            ->rule('regex:/^09\d{9}$/'),

                        TextInput::make('email')->label('رایانامه')->email()->maxLength(180),

                        DatePicker::make('birth_date')
                            ->label('تاریخ تولد')
                            ->required()
                            ->maxDate(now())
                            ->helperText('میلادی وارد کنید؛ نمایش عمومی شمسی است.'),

                        Select::make('gender')
                            ->label('جنسیت')
                            ->options(collect(Gender::options())->except('mixed')->all())
                            ->required()
                            ->native(false),

                        TextInput::make('guardian_name')
                            ->label('نام ولی')
                            ->maxLength(120)
                            ->helperText('برای متقاضیان زیر ۱۸ سال الزامی است.'),

                        TextInput::make('emergency_phone')->label('تلفن اضطراری')->tel(),

                        Toggle::make('has_insurance')->label('بیمهٔ ورزشی دارد'),

                        Textarea::make('address')->label('نشانی')->rows(2)->columnSpanFull(),

                        Textarea::make('medical_notes')
                            ->label('سوابق یا محدودیت پزشکی')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('وضعیت پرونده')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('reference')
                            ->label('کد پیگیری')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('به‌صورت خودکار ساخته می‌شود.'),

                        Select::make('training_class_id')
                            ->label('کلاس')
                            ->relationship('trainingClass', 'title')
                            ->required()
                            ->preload()
                            ->searchable(),

                        Select::make('status')
                            ->label('وضعیت')
                            ->options(EnrollmentStatus::options())
                            ->default(EnrollmentStatus::Pending->value)
                            ->required()
                            ->native(false),

                        TextInput::make('amount')
                            ->label('مبلغ')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix('تومان'),

                        Select::make('user_id')
                            ->label('حساب کاربری مرتبط')
                            ->relationship('user', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText('در صورت اتصال، ثبت‌نام در پرتال ورزشکار دیده می‌شود.'),

                        Textarea::make('admin_notes')
                            ->label('یادداشت داخلی')
                            ->rows(3)
                            ->helperText('برای متقاضی نمایش داده نمی‌شود.'),
                    ]),
            ])
            ->columns(3);
    }
}
