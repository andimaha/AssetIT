<?php

namespace App\Filament\Resources\UserManagements;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\UserManagements\Pages\CreateUserManagement;
use App\Filament\Resources\UserManagements\Pages\EditUserManagement;
use App\Filament\Resources\UserManagements\Pages\ListUserManagements;
use App\Filament\Resources\UserManagements\Schemas\UserManagementForm;
use App\Filament\Resources\UserManagements\Tables\UserManagementsTable;
use App\Models\User;

use BackedEnum;

use Filament\Schemas\Schema;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Model;


class UserManagementResource extends BaseResource
{
    protected static ?string $model =
        User::class;


    protected static string $permissionPrefix =
        'usermanagement';


    /**
     * ==========================================================
     * NAVIGATION
     * ==========================================================
     */

    protected static bool $shouldRegisterNavigation =
        true;


    protected static ?string $navigationLabel =
        'User Management';


    protected static ?string $modelLabel =
        'User';


    protected static ?string $pluralModelLabel =
        'Users';


    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-users';


    protected static string|\UnitEnum|null $navigationGroup =
        'Administration';


    protected static ?int $navigationSort =
        0;


    /**
     * ==========================================================
     * AUTHORIZATION
     * ==========================================================
     *
     * User Management hanya dapat digunakan oleh
     * super_admin.
     *
     * Super Admin tidak dapat dikelola melalui
     * User Management.
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
            && ! $record->hasRole(
                'super_admin'
            );
    }


    public static function canDelete(
        Model $record
    ): bool {

        return auth()->check()
            && auth()->user()->hasRole(
                'super_admin'
            )
            && ! $record->hasRole(
                'super_admin'
            )
            && $record->id !== auth()->id();
    }


    public static function canDeleteAny(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole(
                'super_admin'
            );
    }


    /**
     * ==========================================================
     * FORM
     * ==========================================================
     */

    public static function form(
        Schema $schema
    ): Schema {

        return UserManagementForm::configure(
            $schema
        );
    }


    /**
     * ==========================================================
     * TABLE
     * ==========================================================
     */

    public static function table(
        Table $table
    ): Table {

        return UserManagementsTable::configure(
            $table
        );
    }


    /**
     * ==========================================================
     * RELATIONS
     * ==========================================================
     */

    public static function getRelations(): array
    {
        return [];
    }


    /**
     * ==========================================================
     * PAGES
     * ==========================================================
     */

    public static function getPages(): array
    {
        return [

            'index' =>
                ListUserManagements::route('/'),

            'create' =>
                CreateUserManagement::route('/create'),

            'edit' =>
                EditUserManagement::route(
                    '/{record}/edit'
                ),

        ];
    }
}