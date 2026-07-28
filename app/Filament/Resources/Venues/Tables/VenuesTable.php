<?php

namespace App\Filament\Resources\Venues\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VenuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->columns([
                ImageColumn::make('image')
                    ->label('تصویر'),
                TextColumn::make('name')
                    ->label('نام سالن')
                    ->searchable(),
                TextColumn::make('tatami_area')
                    ->label('تاتامی')
                    ->suffix(' م²')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('capacity')
                    ->label('ظرفیت')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('session_rate')
                    ->label('اجارهٔ سانس')
                    ->formatStateUsing(fn (int $state): string => toman($state))
                    ->sortable(),
                TextColumn::make('slots_count')
                    ->label('سانس هفتگی')
                    ->counts('slots'),
                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
                TextColumn::make('order')
                    ->label('ترتیب')
                    ->numeric()
                    ->sortable(),
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
