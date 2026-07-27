<?php

namespace App\Filament\Resources\TrxSoftwareAssignments;

use App\Filament\Resources\TrxSoftwareAssignments\Pages\CreateTrxSoftwareAssignment;
use App\Filament\Resources\TrxSoftwareAssignments\Pages\EditTrxSoftwareAssignment;
use App\Filament\Resources\TrxSoftwareAssignments\Pages\ListTrxSoftwareAssignments;
use App\Filament\Resources\TrxSoftwareAssignments\Schemas\TrxSoftwareAssignmentForm;
use App\Filament\Resources\TrxSoftwareAssignments\Tables\TrxSoftwareAssignmentsTable;
use App\Models\TrxSoftwareAssignment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TrxSoftwareAssignmentResource extends Resource
{
    protected static ?string $model = TrxSoftwareAssignment::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'Software Assignment';

    protected static ?string $modelLabel = 'Software Assignment';

    protected static ?string $pluralModelLabel = 'Software Assignment';

    protected static string|\UnitEnum|null $navigationGroup = 'Software Management';

    public static function form(Schema $schema): Schema
    {
        return TrxSoftwareAssignmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrxSoftwareAssignmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrxSoftwareAssignments::route('/'),
            'create' => CreateTrxSoftwareAssignment::route('/create'),
            'edit' => EditTrxSoftwareAssignment::route('/{record}/edit'),
        ];
    }
}