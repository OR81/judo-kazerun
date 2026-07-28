<?php

namespace App\Filament\Resources\VenueSlots\Pages;

use App\Filament\Resources\VenueSlots\VenueSlotResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVenueSlot extends EditRecord
{
    protected static string $resource = VenueSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
