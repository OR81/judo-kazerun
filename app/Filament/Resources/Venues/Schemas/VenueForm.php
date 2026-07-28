<?php

namespace App\Filament\Resources\Venues\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VenueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام سالن')
                    ->required()
                    ->live(onBlur: true)
                    // fa_slug keeps the Persian characters; Str::slug() would strip them.
                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', fa_slug((string) $state))),
                TextInput::make('slug')
                    ->label('نشانی یکتا')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('tagline')
                    ->label('توضیح کوتاه')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('معرفی سالن')
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('tatami_area')
                    ->label('مساحت تاتامی (متر مربع)')
                    ->numeric()
                    ->required()
                    ->default(0),
                TextInput::make('capacity')
                    ->label('ظرفیت هم‌زمان (نفر)')
                    ->numeric()
                    ->required()
                    ->default(0),
                TextInput::make('session_rate')
                    ->label('اجارهٔ هر سانس (تومان)')
                    ->helperText('نرخ پیش‌فرض سالن؛ هر سانس می‌تواند نرخ خودش را داشته باشد.')
                    ->numeric()
                    ->required()
                    ->default(0),

                Repeater::make('features')
                    ->label('امکانات')
                    ->simple(TextInput::make('feature')->required())
                    ->addActionLabel('افزودن امکان')
                    ->default([])
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label('تصویر سالن')
                    ->image()
                    ->directory('venues')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
                TextInput::make('order')
                    ->label('ترتیب نمایش')
                    ->numeric()
                    ->required()
                    ->default(0),
            ]);
    }
}
