<?php

namespace App\Filament\Resources\Athletes\Schemas;

use App\Enums\Gender;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AthleteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Select::make('belt_id')
                    ->relationship('belt', 'name'),
                Select::make('coach_id')
                    ->relationship('coach', 'name'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('photo'),
                DatePicker::make('birth_date'),
                Select::make('gender')
                    ->options(Gender::class)
                    ->default('male')
                    ->required(),
                TextInput::make('weight_class'),
                TextInput::make('club'),
                TextInput::make('city'),
                Textarea::make('bio')
                    ->columnSpanFull(),
                Toggle::make('is_national_team')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
