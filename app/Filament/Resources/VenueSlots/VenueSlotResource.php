<?php

namespace App\Filament\Resources\VenueSlots;

use App\Filament\Resources\VenueSlots\Pages\CreateVenueSlot;
use App\Filament\Resources\VenueSlots\Pages\EditVenueSlot;
use App\Filament\Resources\VenueSlots\Pages\ListVenueSlots;
use App\Filament\Resources\VenueSlots\Schemas\VenueSlotForm;
use App\Filament\Resources\VenueSlots\Tables\VenueSlotsTable;
use App\Models\VenueSlot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VenueSlotResource extends Resource
{
    protected static ?string $model = VenueSlot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'خانهٔ جودو';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'سانس';

    protected static ?string $pluralModelLabel = 'سانس‌های سالن';

    protected static ?string $recordTitleAttribute = 'holder';

    /** How many slots are currently free to rent — the number the office cares about. */
    public static function getNavigationBadge(): ?string
    {
        return fa((string) VenueSlot::query()->open()->count());
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'سانس‌های آزاد برای اجاره';
    }

    public static function form(Schema $schema): Schema
    {
        return VenueSlotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VenueSlotsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVenueSlots::route('/'),
            'create' => CreateVenueSlot::route('/create'),
            'edit' => EditVenueSlot::route('/{record}/edit'),
        ];
    }
}
