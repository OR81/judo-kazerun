<?php

namespace App\Filament\Resources\Enrollments\Tables;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class EnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')
                    ->label('کد پیگیری')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('کد پیگیری رونوشت شد')
                    ->fontFamily('mono')
                    ->size(TextSize::Small),

                TextColumn::make('full_name')
                    ->label('متقاضی')
                    ->searchable(['first_name', 'last_name'])
                    ->description(fn (Enrollment $record) => fa($record->mobile)),

                TextColumn::make('trainingClass.title')
                    ->label('کلاس')
                    ->badge()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('مبلغ')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state > 0 ? toman($state) : 'رایگان'),

                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (EnrollmentStatus $state) => $state->label())
                    ->color(fn (EnrollmentStatus $state) => match ($state) {
                        EnrollmentStatus::Approved => 'success',
                        EnrollmentStatus::Paid => 'info',
                        EnrollmentStatus::Rejected => 'danger',
                        EnrollmentStatus::AwaitingPayment => 'warning',
                        EnrollmentStatus::Pending => 'gray',
                    }),

                TextColumn::make('documents_count')
                    ->label('مدارک')
                    ->counts('documents')
                    ->formatStateUsing(fn ($state) => fa_number($state)),

                TextColumn::make('created_at')
                    ->label('تاریخ ثبت')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => shamsi($state)),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(EnrollmentStatus::options())
                    ->multiple(),

                SelectFilter::make('training_class_id')
                    ->label('کلاس')
                    ->relationship('trainingClass', 'title')
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('تأیید')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأیید ثبت‌نام')
                    ->modalDescription('پس از تأیید، ثبت‌نام در پرتال ورزشکار و فهرست مربی نمایش داده می‌شود.')
                    // Only offer it where it makes sense.
                    ->visible(fn (Enrollment $record) => in_array(
                        $record->status,
                        [EnrollmentStatus::Pending, EnrollmentStatus::Paid],
                        true,
                    ))
                    ->action(function (Enrollment $record) {
                        $record->update([
                            'status' => EnrollmentStatus::Approved,
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->title('ثبت‌نام تأیید شد')
                            ->body($record->full_name.' به کلاس '.$record->trainingClass->title.' افزوده شد.')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('رد')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Enrollment $record) => $record->status !== EnrollmentStatus::Rejected)
                    ->schema([
                        Textarea::make('admin_notes')
                            ->label('دلیل رد درخواست')
                            ->required()
                            ->rows(3)
                            ->helperText('این یادداشت داخلی است و برای متقاضی نمایش داده نمی‌شود.'),
                    ])
                    ->action(function (Enrollment $record, array $data) {
                        $record->update([
                            'status' => EnrollmentStatus::Rejected,
                            'admin_notes' => $data['admin_notes'],
                        ]);

                        Notification::make()->title('ثبت‌نام رد شد')->warning()->send();
                    }),

                EditAction::make()->label('ویرایش'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveSelected')
                        ->label('تأیید موارد انتخابی')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update([
                                'status' => EnrollmentStatus::Approved,
                                'approved_at' => now(),
                            ]);

                            Notification::make()
                                ->title(fa_number($records->count()).' ثبت‌نام تأیید شد')
                                ->success()
                                ->send();
                        }),

                    DeleteBulkAction::make()->label('حذف'),
                ]),
            ])
            ->emptyStateHeading('ثبت‌نامی ثبت نشده است');
    }
}
