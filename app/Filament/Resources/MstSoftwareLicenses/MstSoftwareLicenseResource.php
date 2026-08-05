<?php

namespace App\Filament\Resources\MstSoftwareLicenses;

use App\Filament\Resources\MstSoftwareLicenses\Pages\CreateMstSoftwareLicense;
use App\Filament\Resources\MstSoftwareLicenses\Pages\EditMstSoftwareLicense;
use App\Filament\Resources\MstSoftwareLicenses\Pages\ListMstSoftwareLicenses;
use App\Filament\Resources\MstSoftwareLicenses\RelationManagers\AssignmentRelationManager;
use App\Filament\Resources\MstSoftwareLicenses\Schemas\MstSoftwareLicenseForm;
use App\Filament\Resources\MstSoftwareLicenses\Tables\MstSoftwareLicensesTable;
use App\Models\MstSoftwareLicense;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MstSoftwareLicenseResource extends Resource
{
    protected static ?string $model = MstSoftwareLicense::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'Software License';

    protected static ?string $modelLabel = 'Software License';

    protected static ?string $pluralModelLabel = 'Software License';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|\UnitEnum|null $navigationGroup = 'Software Management';

    /*
    |--------------------------------------------------------------------------
    | Navigation Order
    |--------------------------------------------------------------------------
    |
    | Software         = 1
    | Software License = 2
    |
    */

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MstSoftwareLicenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MstSoftwareLicensesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [

            AssignmentRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [

            'index' => ListMstSoftwareLicenses::route('/'),

            'create' => CreateMstSoftwareLicense::route('/create'),

            'edit' => EditMstSoftwareLicense::route('/{record}/edit'),

        ];
    }
}