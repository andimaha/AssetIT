<?php

namespace App\Filament\Resources\RoleManagements\Pages;

use App\Filament\Resources\RoleManagements\RoleManagementResource;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;


class EditRoleManagement extends EditRecord
{
    protected static string $resource =
        RoleManagementResource::class;


    protected array $selectedPermissions = [];


    protected array $oldPermissions = [];


    /*
    |--------------------------------------------------------------------------
    | BEFORE FILL
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeFill(
        array $data
    ): array {

        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        */

        if (
            $this->record->name === 'super_admin'
        ) {

            return $data;
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL PERMISSION ROLE
        |--------------------------------------------------------------------------
        */

        $this->oldPermissions =
            $this->record
                ->permissions()
                ->where(
                    'guard_name',
                    'web'
                )
                ->pluck(
                    'name'
                )
                ->filter()
                ->unique()
                ->values()
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | MASTER DATA
        |--------------------------------------------------------------------------
        */

        $data['permissions_mst'] =
            collect(
                $this->oldPermissions
            )
                ->filter(
                    fn (
                        string $permission
                    ): bool =>
                        str_starts_with(
                            $permission,
                            'mst'
                        )
                )
                ->values()
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $data['permissions_trx'] =
            collect(
                $this->oldPermissions
            )
                ->filter(
                    fn (
                        string $permission
                    ): bool =>
                        str_starts_with(
                            $permission,
                            'trx'
                        )
                )
                ->values()
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | IT REQUEST
        |--------------------------------------------------------------------------
        */

        $data['permissions_itrequest'] =
            collect(
                $this->oldPermissions
            )
                ->filter(
                    fn (
                        string $permission
                    ): bool =>
                        str_starts_with(
                            $permission,
                            'itrequest'
                        )
                )
                ->values()
                ->toArray();


        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | BEFORE SAVE
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        */

        if (
            $this->record->name === 'super_admin'
        ) {

            return $data;
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
        | SECURITY
        |--------------------------------------------------------------------------
        |
        | Role super_admin tidak boleh dibuat dari edit.
        |
        */

        if (
            ($data['name'] ?? null)
            === 'super_admin'
        ) {

            $data['name'] =
                $this->record->name;
        }


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
    | AFTER SAVE
    |--------------------------------------------------------------------------
    */

    protected function afterSave(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        */

        if (
            $this->record->name === 'super_admin'
        ) {

            Notification::make()

                ->title(
                    'Super Admin tidak dapat diubah'
                )

                ->body(
                    'Role super_admin dikelola secara khusus dan tidak dapat diubah melalui Role Management.'
                )

                ->danger()

                ->send();

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
        | PERMISSION DIUBAH?
        |--------------------------------------------------------------------------
        */

        $oldPermissions =
            collect(
                $this->oldPermissions
            );


        $newPermissions =
            collect(
                $this->selectedPermissions
            );


        $added =
            $newPermissions

                ->diff(
                    $oldPermissions
                )

                ->values()

                ->toArray();


        $removed =
            $oldPermissions

                ->diff(
                    $newPermissions
                )

                ->values()

                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOG
        |--------------------------------------------------------------------------
        */

        if (
            count($added) > 0
            ||
            count($removed) > 0
        ) {

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

                    'old_permissions' =>
                        $oldPermissions
                            ->values()
                            ->toArray(),

                    'new_permissions' =>
                        $newPermissions
                            ->values()
                            ->toArray(),

                    'added' =>
                        $added,

                    'removed' =>
                        $removed,

                    'ip_address' =>
                        request()->ip(),

                    'user_agent' =>
                        request()->userAgent(),

                ])

                ->log(
                    'Permission role diperbarui'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | NOTIFICATION
        |--------------------------------------------------------------------------
        */

        Notification::make()

            ->title(
                'Role berhasil diperbarui'
            )

            ->body(
                'Permission role '
                . $this->record->name
                . ' berhasil diperbarui.'
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
        return 'Edit Role: ' .
            $this->record->name;
    }


    /*
    |--------------------------------------------------------------------------
    | HEADER ACTIONS
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [];
    }
}
