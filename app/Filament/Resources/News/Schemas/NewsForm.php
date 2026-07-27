<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('محتوای خبر')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(180)
                            ->live(onBlur: true)
                            // Only auto-slug while creating, so published URLs never move.
                            ->afterStateUpdated(function (string $operation, $state, callable $set) {
                                if ($operation === 'create') {
                                    $set('slug', fa_slug((string) $state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('نشانی یکتا (اسلاگ)')
                            ->required()
                            ->maxLength(200)
                            ->unique(ignoreRecord: true)
                            ->helperText('در نشانی صفحه استفاده می‌شود. تغییر آن پیوندهای قبلی را می‌شکند.'),

                        Textarea::make('excerpt')
                            ->label('خلاصه')
                            ->rows(3)
                            ->maxLength(400)
                            ->helperText('در کارت خبر و نتایج جستجو نمایش داده می‌شود.'),

                        RichEditor::make('body')
                            ->label('متن کامل')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('انتشار')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('news_category_id')
                            ->label('دسته‌بندی')
                            ->relationship('category', 'name')
                            ->required()
                            ->preload()
                            ->searchable(),

                        Select::make('author_id')
                            ->label('نویسنده')
                            ->relationship('author', 'name')
                            ->default(fn () => auth()->id())
                            ->preload()
                            ->searchable(),

                        DateTimePicker::make('published_at')
                            ->label('زمان انتشار')
                            ->seconds(false)
                            ->default(now())
                            ->helperText('خالی بگذارید تا پیش‌نویس بماند. تاریخ آینده یعنی انتشار زمان‌بندی‌شده.'),

                        Toggle::make('is_featured')
                            ->label('مطلب ویژه')
                            ->helperText('در بالای صفحهٔ اخبار و صفحهٔ اصلی برجسته می‌شود.'),

                        TextInput::make('read_minutes')
                            ->label('زمان مطالعه (دقیقه)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(60)
                            ->default(3),

                        FileUpload::make('cover_image')
                            ->label('تصویر شاخص')
                            ->image()
                            ->imageEditor()
                            ->directory('news')
                            ->disk('public')
                            ->maxSize(4096)
                            ->helperText('نسبت پیشنهادی ۱۶:۹ — حداکثر ۴ مگابایت.'),

                        TextInput::make('views')
                            ->label('بازدید')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn (?Model $record) => $record !== null),
                    ]),
            ])
            ->columns(3);
    }
}
