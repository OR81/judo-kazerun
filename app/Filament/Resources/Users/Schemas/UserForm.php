<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\Gender;
use App\Enums\UserRole;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                Select::make('role')
                    ->options(UserRole::class)
                    ->default('athlete')
                    ->required(),
                TextInput::make('mobile'),
                TextInput::make('national_code'),
                DatePicker::make('birth_date'),
                Select::make('gender')
                    ->options(Gender::class),
                TextInput::make('avatar'),
                TextInput::make('city'),
                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('last_login_at'),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Textarea::make('two_factor_secret')
                    ->columnSpanFull(),
                Textarea::make('two_factor_recovery_codes')
                    ->columnSpanFull(),
                DateTimePicker::make('two_factor_confirmed_at'),
            ]);
    }
}
