<?php

namespace App\Filament\Resources\VenueSlots\Schemas;

use App\Enums\Gender;
use App\Enums\SlotStatus;
use App\Support\PersianNumber;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class VenueSlotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('venue_id')
                    ->label('سالن')
                    ->relationship('venue', 'name')
                    ->required(),

                Select::make('day_of_week')
                    ->label('روز هفته')
                    ->options(PersianNumber::weekdays())
                    ->required(),

                TimePicker::make('start_time')
                    ->label('از ساعت')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('end_time')
                    ->label('تا ساعت')
                    ->seconds(false)
                    ->required()
                    ->after('start_time'),

                Select::make('status')
                    ->label('وضعیت')
                    ->options(SlotStatus::options())
                    ->default(SlotStatus::Open->value)
                    ->live()
                    ->required(),

                Select::make('gender')
                    ->label('ویژهٔ')
                    ->options(Gender::options())
                    ->default(Gender::Mixed->value)
                    ->required(),

                Select::make('training_class_id')
                    ->label('کلاس هیئت')
                    ->relationship('trainingClass', 'title')
                    ->searchable()
                    ->preload()
                    ->helperText('فقط برای سانس‌هایی که کلاس خودِ هیئت هستند.')
                    ->visible(fn (callable $get): bool => $get('status') === SlotStatus::BoardClass->value),

                TextInput::make('holder')
                    ->label('رزروکننده')
                    ->helperText('نام باشگاه، مدرسه یا گروهی که سانس را اجاره کرده است.')
                    ->visible(fn (callable $get): bool => $get('status') === SlotStatus::Booked->value),

                TextInput::make('price')
                    ->label('اجارهٔ این سانس (تومان)')
                    ->helperText('خالی بگذارید تا نرخ پیش‌فرض سالن اعمال شود.')
                    ->numeric(),

                TextInput::make('note')
                    ->label('یادداشت')
                    ->columnSpanFull(),
            ]);
    }
}
