<?php

namespace App\Filament\Resources\Downloads\Schemas;

use App\Enums\DownloadCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DownloadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->options(DownloadCategory::class)
                    ->default('form')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('file_name')
                    ->required(),
                TextInput::make('extension'),
                TextInput::make('size')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('downloads_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
