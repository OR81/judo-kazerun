<?php

namespace App\Filament\Resources\VenueSlots\Pages;

use App\Filament\Resources\VenueSlots\VenueSlotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVenueSlots extends ListRecords
{
    protected static string $resource = VenueSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
