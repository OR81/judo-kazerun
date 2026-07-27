<?php

namespace App\Filament\Resources\TrainingClasses;

use App\Filament\Resources\TrainingClasses\Pages\CreateTrainingClass;
use App\Filament\Resources\TrainingClasses\Pages\EditTrainingClass;
use App\Filament\Resources\TrainingClasses\Pages\ListTrainingClasses;
use App\Filament\Resources\TrainingClasses\Schemas\TrainingClassForm;
use App\Filament\Resources\TrainingClasses\Tables\TrainingClassesTable;
use App\Models\TrainingClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TrainingClassResource extends Resource
{
    protected static ?string $model = TrainingClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'آموزش و ثبت‌نام';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'کلاس تمرینی';

    protected static ?string $pluralModelLabel = 'کلاس‌های تمرینی';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return TrainingClassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingClassesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingClasses::route('/'),
            'create' => CreateTrainingClass::route('/create'),
            'edit' => EditTrainingClass::route('/{record}/edit'),
        ];
    }
}
