<?php

namespace App\Filament\Resources\Enrollments;

use App\Enums\EnrollmentStatus;
use App\Filament\Resources\Enrollments\Pages\CreateEnrollment;
use App\Filament\Resources\Enrollments\Pages\EditEnrollment;
use App\Filament\Resources\Enrollments\Pages\ListEnrollments;
use App\Filament\Resources\Enrollments\Schemas\EnrollmentForm;
use App\Filament\Resources\Enrollments\Tables\EnrollmentsTable;
use App\Models\Enrollment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'آموزش و ثبت‌نام';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'ثبت‌نام';

    protected static ?string $pluralModelLabel = 'ثبت‌نام‌ها';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function form(Schema $schema): Schema
    {
        return EnrollmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EnrollmentsTable::configure($table);
    }

    /** Applications awaiting a decision — the number staff act on daily. */
    public static function getNavigationBadge(): ?string
    {
        $waiting = static::getModel()::query()
            ->whereIn('status', [EnrollmentStatus::Pending->value, EnrollmentStatus::Paid->value])
            ->count();

        return $waiting > 0 ? fa_number($waiting) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEnrollments::route('/'),
            'create' => CreateEnrollment::route('/create'),
            'edit' => EditEnrollment::route('/{record}/edit'),
        ];
    }
}
