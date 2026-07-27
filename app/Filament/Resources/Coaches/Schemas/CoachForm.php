<?php

namespace App\Filament\Resources\Coaches\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CoachForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Select::make('belt_id')
                    ->relationship('belt', 'name'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('photo'),
                TextInput::make('title'),
                TextInput::make('dan_rank')
                    ->numeric(),
                Textarea::make('summary')
                    ->columnSpanFull(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                TextInput::make('experience_years')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('specialties'),
                TextInput::make('certificates'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('instagram'),
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
