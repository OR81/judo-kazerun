<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\EventType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Select::make('type')
                    ->options(EventType::class)
                    ->default('competition')
                    ->required(),
                Textarea::make('summary')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('poster'),
                TextInput::make('location'),
                TextInput::make('organizer'),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('ends_at'),
                DateTimePicker::make('registration_deadline'),
                TextInput::make('capacity')
                    ->numeric(),
                TextInput::make('fee')
                    ->numeric(),
                TextInput::make('age_groups'),
                TextInput::make('status')
                    ->required()
                    ->default('scheduled'),
                Toggle::make('is_featured')
                    ->required(),
            ]);
    }
}
