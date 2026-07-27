<?php

namespace App\Filament\Widgets;

use App\Models\TrainingClass;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * Which classes are close to full — the thing the board most often needs to see
 * before deciding whether to open another session.
 */
class ClassCapacity extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'ظرفیت کلاس‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TrainingClass::query()
                    ->where('is_active', true)
                    ->with('coach')
                    // Fullest first.
                    ->orderByRaw('CASE WHEN capacity > 0 THEN enrolled_count * 1.0 / capacity ELSE 0 END DESC')
            )
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('title')
                    ->label('کلاس')
                    ->searchable()
                    ->description(fn (TrainingClass $record) => $record->coach?->name),

                TextColumn::make('age_group')
                    ->label('رده')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label()),

                TextColumn::make('enrolled_count')
                    ->label('ثبت‌نام')
                    ->formatStateUsing(fn ($state, TrainingClass $record) => fa_number($state).' از '.fa_number($record->capacity)),

                TextColumn::make('occupancy_percent')
                    ->label('اشغال')
                    ->badge()
                    ->formatStateUsing(fn ($state) => fa_number($state).'٪')
                    ->color(fn ($state) => match (true) {
                        $state >= 100 => 'danger',
                        $state >= 80 => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('remaining_seats')
                    ->label('جای خالی')
                    ->formatStateUsing(fn ($state) => $state > 0 ? fa_number($state) : 'تکمیل'),

                TextColumn::make('monthly_fee')
                    ->label('شهریه')
                    ->formatStateUsing(fn ($state) => $state > 0 ? toman($state) : 'رایگان'),
            ])
            ->filters([])
            ->emptyStateHeading('کلاس فعالی وجود ندارد');
    }
}
