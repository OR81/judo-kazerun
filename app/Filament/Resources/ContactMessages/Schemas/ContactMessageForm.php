<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContactMessageForm
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
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_read')
                    ->required(),
                DateTimePicker::make('replied_at'),
                TextInput::make('ip_address'),
            ]);
    }
}
