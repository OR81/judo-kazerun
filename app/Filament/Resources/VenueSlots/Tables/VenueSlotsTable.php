<?php

namespace App\Filament\Resources\VenueSlots\Tables;

use App\Enums\Gender;
use App\Enums\SlotStatus;
use App\Support\PersianNumber;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VenueSlotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('day_of_week')
            ->modifyQueryUsing(fn ($query) => $query->with('trainingClass'))
            ->groups(['venue.name'])
            ->columns([
                TextColumn::make('venue.name')
                    ->label('سالن')
                    ->searchable(),
                TextColumn::make('day_of_week')
                    ->label('روز')
                    ->formatStateUsing(fn (int $state): string => PersianNumber::weekday($state))
                    ->sortable(),
                TextColumn::make('time_range')
                    ->label('ساعت'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (SlotStatus $state): string => $state->label())
                    ->color(fn (SlotStatus $state): string => match ($state) {
                        SlotStatus::Open => 'success',
                        SlotStatus::BoardClass => 'primary',
                        SlotStatus::Booked => 'gray',
                        SlotStatus::Closed => 'warning',
                    }),
                TextColumn::make('occupant_label')
                    ->label('رزروکننده / کلاس')
                    ->searchable(['holder'])
                    ->wrap(),
                TextColumn::make('gender')
                    ->label('ویژهٔ')
                    ->badge()
                    ->formatStateUsing(fn (Gender $state): string => $state->label()),
                TextColumn::make('price')
                    ->label('اجاره')
                    ->placeholder('نرخ سالن')
                    ->formatStateUsing(fn (?int $state): string => $state === null ? '—' : toman($state)),
            ])
            ->filters([
                SelectFilter::make('venue')
                    ->label('سالن')
                    ->relationship('venue', 'name'),
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(SlotStatus::options()),
                SelectFilter::make('day_of_week')
                    ->label('روز هفته')
                    ->options(PersianNumber::weekdays()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
