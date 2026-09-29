<?php

namespace App\Filament\Resources\RoleManagements;

use App\Filament\Resources\BaseResource;

use App\Filament\Resources\RoleManagements\Pages\CreateRoleManagement;
use App\Filament\Resources\RoleManagements\Pages\EditRoleManagement;
use App\Filament\Resources\RoleManagements\Pages\ListRoleManagements;

use App\Filament\Resources\RoleManagements\Schemas\RoleManagementForm;
use App\Filament\Resources\RoleManagements\Tables\RoleManagementsTable;

use BackedEnum;

use Filament\Schemas\Schema;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Model;

use Spatie\Permission\Models\Role;


class RoleManagementResource extends BaseResource
{
    protected static ?string $model =
        Role::class;


    /*
    |--------------------------------------------------------------------------
    | SLUG
    |--------------------------------------------------------------------------
    */

    protected static ?string $slug =
        'role-managements';


    /*
    |--------------------------------------------------------------------------
    | PERMISSION PREFIX
    |--------------------------------------------------------------------------
    */

    protected static string $permissionPrefix =
        'rolemanagement';


    /*
    |--------------------------------------------------------------------------
    | NAVIGATION
    |--------------------------------------------------------------------------
    */

    protected static bool $shouldRegisterNavigation =
        true;


    protected static ?string $navigationLabel =
        'Role Management';


    protected static ?string $modelLabel =
        'Role';


    protected static ?string $pluralModelLabel =
        'Roles';


    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-shield-check';


    protected static string|\UnitEnum|null $navigationGroup =
        'Administration';


    protected static ?int $navigationSort =
        1;


    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
    |--------------------------------------------------------------------------
    |
    | Role Management hanya dapat digunakan oleh Super Admin.
    |
    | super_admin sendiri tidak dapat:
    |
    | - diedit
    | - dihapus
    |
    */

    public static function canViewAny(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole(
                'super_admin'
            );
    }


    public static function canView(
        Model $record
    ): bool {

        return auth()->check()
            && auth()->user()->hasRole(
                'super_admin'
            );
    }


    public static function canCreate(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole(
                'super_admin'
            );
    }


    public static function canEdit(
        Model $record
    ): bool {

        return auth()->check()
            && auth()->user()->hasRole(
                'super_admin'
            )
            && $record->name !== 'super_admin';
    }


    public static function canDelete(
        Model $record
    ): bool {

        return auth()->check()
            && auth()->user()->hasRole(
                'super_admin'
            )
            && $record->name !== 'super_admin'
            && ! $record->users()->exists();
    }


    public static function canDeleteAny(): bool
    {
        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public static function form(
        Schema $schema
    ): Schema {

        return RoleManagementForm::configure(
            $schema
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public static function table(
        Table $table
    ): Table {

        return RoleManagementsTable::configure(
            $table
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [];
    }


    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [

            'index' =>
                ListRoleManagements::route('/'),

            'create' =>
                CreateRoleManagement::route('/create'),

            'edit' =>
                EditRoleManagement::route(
                    '/{record}/edit'
                ),

        ];
    }
}
