<?php

namespace App\Filament\Resources\RoleManagements\Pages;

use App\Filament\Resources\RoleManagements\RoleManagementResource;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;


class CreateRoleManagement extends CreateRecord
{
    protected static string $resource =
        RoleManagementResource::class;


    protected array $selectedPermissions = [];


    /*
    |--------------------------------------------------------------------------
    | BEFORE CREATE
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {

        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        |
        | Jangan pernah mengizinkan super_admin dibuat dari UI.
        |
        */

        if (
            ($data['name'] ?? null)
            === 'super_admin'
        ) {

            Notification::make()

                ->title(
                    'Role tidak diperbolehkan'
                )

                ->body(
                    'Role super_admin dikelola secara khusus dan tidak dapat dibuat dari Role Management.'
                )

                ->danger()

                ->send();

            $data['name'] =
                'role_baru';
        }


        /*
        |--------------------------------------------------------------------------
        | PERMISSION
        |--------------------------------------------------------------------------
        */

        $permissions = collect([

            ...(
                $data['permissions_mst']
                ?? []
            ),

            ...(
                $data['permissions_trx']
                ?? []
            ),

            ...(
                $data['permissions_itrequest']
                ?? []
            ),

        ]);


        $this->selectedPermissions =
            $this->normalizePermissions(
                $permissions->toArray()
            );


        /*
        |--------------------------------------------------------------------------
        | HAPUS FIELD NON-ROLES
        |--------------------------------------------------------------------------
        */

        unset(

            $data['permissions_mst'],

            $data['permissions_trx'],

            $data['permissions_itrequest'],

        );


        /*
        |--------------------------------------------------------------------------
        | GUARD
        |--------------------------------------------------------------------------
        */

        $data['guard_name'] =
            'web';


        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE PERMISSIONS
    |--------------------------------------------------------------------------
    */

    protected function normalizePermissions(
        array $permissions
    ): array {

        return collect($permissions)

            ->map(
                function (
                    $permission
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | PERMISSION MODEL
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $permission
                            instanceof Permission
                    ) {

                        return $permission->name;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DATABASE ID
                    |--------------------------------------------------------------------------
                    */

                    if (
                        is_numeric($permission)
                    ) {

                        return Permission::query()

                            ->where(
                                'guard_name',
                                'web'
                            )

                            ->whereKey(
                                $permission
                            )

                            ->value(
                                'name'
                            );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PERMISSION NAME
                    |--------------------------------------------------------------------------
                    */

                    return (string) $permission;
                }
            )

            ->filter()

            ->unique()

            ->values()

            ->toArray();
    }


    /*
    |--------------------------------------------------------------------------
    | AFTER CREATE
    |--------------------------------------------------------------------------
    */

    protected function afterCreate(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        */

        if (
            $this->record->name === 'super_admin'
        ) {

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SYNC PERMISSION
        |--------------------------------------------------------------------------
        */

        $this->record->syncPermissions(
            $this->selectedPermissions
        );


        /*
        |--------------------------------------------------------------------------
        | CLEAR CACHE
        |--------------------------------------------------------------------------
        */

        app(
            PermissionRegistrar::class
        )->forgetCachedPermissions();


        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOG
        |--------------------------------------------------------------------------
        */

        activity('role_management')

            ->causedBy(
                auth()->user()
            )

            ->performedOn(
                $this->record
            )

            ->withProperties([

                'role_id' =>
                    $this->record->id,

                'role_name' =>
                    $this->record->name,

                'permissions' =>
                    $this->selectedPermissions,

                'ip_address' =>
                    request()->ip(),

                'user_agent' =>
                    request()->userAgent(),

            ])

            ->log(
                'Role baru dibuat'
            );


        /*
        |--------------------------------------------------------------------------
        | NOTIFICATION
        |--------------------------------------------------------------------------
        */

        Notification::make()

            ->title(
                'Role berhasil dibuat'
            )

            ->body(
                'Role '
                . $this->record->name
                . ' berhasil dibuat dengan '
                . count(
                    $this->selectedPermissions
                )
                . ' permission.'
            )

            ->success()

            ->send();
    }


    /*
    |--------------------------------------------------------------------------
    | TITLE
    |--------------------------------------------------------------------------
    */

    public function getTitle(): string
    {
        return 'Tambah Role';
    }
}
