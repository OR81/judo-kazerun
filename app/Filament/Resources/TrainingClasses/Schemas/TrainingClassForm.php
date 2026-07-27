<?php

namespace App\Filament\Resources\TrainingClasses\Schemas;

use App\Enums\AgeGroup;
use App\Enums\ClassLevel;
use App\Enums\Gender;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrainingClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('coach_id')
                    ->relationship('coach', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Select::make('age_group')
                    ->options(AgeGroup::class)
                    ->default('kids')
                    ->required(),
                Select::make('gender')
                    ->options(Gender::class)
                    ->default('mixed')
                    ->required(),
                Select::make('level')
                    ->options(ClassLevel::class)
                    ->default('beginner')
                    ->required(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric()
                    ->default(20),
                TextInput::make('enrolled_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('monthly_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('venue'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
