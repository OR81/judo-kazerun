<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SponsorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('logo')
                    ->required(),
                TextInput::make('url')
                    ->url(),
                TextInput::make('tier')
                    ->required()
                    ->default('partner'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
