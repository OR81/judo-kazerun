<?php

namespace App\Filament\Resources\Belts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BeltForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('color')
                    ->required(),
                TextInput::make('dan_level')
                    ->numeric(),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
