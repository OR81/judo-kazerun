<?php

namespace App\Filament\Resources\News\Tables;

use App\Models\News;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('تصویر')
                    ->imageSize(56)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(60)
                    ->description(fn (News $record) => $record->excerpt
                        ? mb_substr($record->excerpt, 0, 70).'…'
                        : null),

                TextColumn::make('category.name')
                    ->label('دسته')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('ویژه')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('انتشار')
                    ->sortable()
                    // Shamsi in the table; the underlying column stays Gregorian.
                    ->formatStateUsing(fn ($state) => $state ? shamsi($state) : 'پیش‌نویس')
                    ->badge()
                    ->color(fn (News $record) => match (true) {
                        $record->published_at === null => 'warning',
                        $record->published_at->isFuture() => 'info',
                        default => 'success',
                    }),

                TextColumn::make('views')
                    ->label('بازدید')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => fa_number($state)),

                TextColumn::make('author.name')
                    ->label('نویسنده')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('news_category_id')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'name')
                    ->preload(),

                TernaryFilter::make('is_featured')->label('مطلب ویژه'),

                Filter::make('drafts')
                    ->label('فقط پیش‌نویس‌ها')
                    ->query(fn (Builder $query) => $query->whereNull('published_at')),

                Filter::make('scheduled')
                    ->label('زمان‌بندی‌شده برای آینده')
                    ->query(fn (Builder $query) => $query->where('published_at', '>', now())),
            ])
            ->recordActions([
                EditAction::make()->label('ویرایش'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف'),
                ]),
            ])
            ->emptyStateHeading('هنوز خبری ثبت نشده است')
            ->emptyStateDescription('نخستین خبر هیئت را با دکمهٔ بالا ایجاد کنید.');
    }
}
