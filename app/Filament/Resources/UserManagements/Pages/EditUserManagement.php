<?php

namespace App\Filament\Resources\UserManagements\Pages;

use App\Filament\Resources\UserManagements\UserManagementResource;
use App\Models\MstKaryawan;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;


class EditUserManagement extends EditRecord
{
    protected static string $resource =
        UserManagementResource::class;


    protected array $selectedRole = [];


    protected array $selectedPermissions = [];


    protected array $oldPermissions = [];


    protected ?string $oldRole = null;


    protected ?string $oldNIK = null;


    /*
    |--------------------------------------------------------------------------
    | OLD KEPALA BAGIAN
    |--------------------------------------------------------------------------
    |
    | Kepala Bagian sekarang berasal dari:
    |
    | users.NIK
    |      ↓
    | mstkaryawan.NIK
    |      ↓
    | mstkaryawan.NIKKepalaBagian
    |      ↓
    | mstkaryawan.NIK
    |
    */

    protected ?string $oldKepalaBagianNIK = null;


    protected ?string $oldKepalaBagianName = null;


    protected ?string $oldKepalaBagianEmail = null;


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
        | OLD NIK
        |--------------------------------------------------------------------------
        */

        $this->oldNIK =
            $this->record->NIK;


        /*
        |--------------------------------------------------------------------------
        | AMBIL KARYAWAN
        |--------------------------------------------------------------------------
        |
        | User terhubung ke MstKaryawan melalui NIK.
        |
        */

        $karyawan =
            MstKaryawan::query()
                ->with([
                    'kepalaBagian',
                    'kepalaBagian.user',
                    'departemen',
                    'perusahaan',
                ])
                ->where(
                    'NIK',
                    $this->record->NIK
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | OLD KEPALA BAGIAN
        |--------------------------------------------------------------------------
        */

        $this->oldKepalaBagianNIK =
            $karyawan
                ?->NIKKepalaBagian;


        $this->oldKepalaBagianName =
            $karyawan
                ?->kepalaBagian
                ?->Nama;


        $this->oldKepalaBagianEmail =
            $karyawan
                ?->kepalaBagian
                ?->user
                ?->email;


        /*
        |--------------------------------------------------------------------------
        | ROLE
        |--------------------------------------------------------------------------
        */

        $this->oldRole =
            $this->record
                ->roles()
                ->where(
                    'guard_name',
                    'web'
                )
                ->value(
                    'name'
                );


        $data['role'] =
            $this->oldRole
            ?? 'user';


        /*
        |--------------------------------------------------------------------------
        | DIRECT PERMISSIONS
        |--------------------------------------------------------------------------
        |
        | Ambil permission yang benar-benar dimiliki langsung oleh user.
        |
        */

        $this->oldPermissions =
            $this->record
                ->getDirectPermissions()
                ->pluck('name')
                ->filter()
                ->unique()
                ->values()
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | MASTER DATA PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $data['permissions_mst'] =
            collect(
                $this->oldPermissions
            )
                ->filter(
                    fn (string $permission): bool =>
                        str_starts_with(
                            $permission,
                            'mst'
                        )
                )
                ->values()
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $data['permissions_trx'] =
            collect(
                $this->oldPermissions
            )
                ->filter(
                    fn (string $permission): bool =>
                        str_starts_with(
                            $permission,
                            'trx'
                        )
                )
                ->values()
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | IT REQUEST PERMISSIONS
        |--------------------------------------------------------------------------
        |
        | FIX:
        |
        | Sebelumnya permissions_itrequest tidak diisi kembali
        | ketika halaman Edit dibuka.
        |
        | Akibatnya permission sebenarnya sudah tersimpan di database,
        | tetapi checkbox terlihat tidak tercentang.
        |
        */

        $data['permissions_itrequest'] =
            collect(
                $this->oldPermissions
            )
                ->filter(
                    fn (string $permission): bool =>
                        str_starts_with(
                            $permission,
                            'itrequest'
                        )
                )
                ->values()
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | KEPALA BAGIAN
        |--------------------------------------------------------------------------
        |
        | TIDAK mengisi:
        |
        | kepala_bagian_id
        |
        | karena field tersebut sudah tidak digunakan.
        |
        | Kepala Bagian dikelola langsung pada:
        |
        | mstkaryawan.NIKKepalaBagian
        |
        */


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
        | ROLE
        |--------------------------------------------------------------------------
        */

        $this->selectedRole = [

            (string) (
                $data['role']
                ?? 'user'
            ),

        ];


        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        |
        | Gabungkan seluruh permission dari form.
        |
        | 1. Master Data
        | 2. Transaction
        | 3. IT Request
        |
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
        | HAPUS FIELD NON-USERS
        |--------------------------------------------------------------------------
        |
        | role dan permission hanya digunakan oleh form
        | dan tidak disimpan langsung ke users.
        |
        | Kepala Bagian juga bukan kolom users.
        |
        */

        unset(

            $data['role'],

            $data['permissions_mst'],

            $data['permissions_trx'],

            $data['permissions_itrequest'],

            $data['kepala_bagian_id'],

        );


        /*
        |--------------------------------------------------------------------------
        | NAMA MENGIKUTI MASTER KARYAWAN
        |--------------------------------------------------------------------------
        |
        | Jika NIK berubah, nama user mengikuti master karyawan.
        |
        */

        if (
            filled($data['NIK'] ?? null)
        ) {

            $karyawan =
                MstKaryawan::query()
                    ->where(
                        'NIK',
                        $data['NIK']
                    )
                    ->first();


            if ($karyawan) {

                $data['name'] =
                    $karyawan->Nama;

            }

        }


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
                function ($permission) {

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
            $this->record
                ->hasRole('super_admin')
        ) {

            Notification::make()

                ->title(
                    'Tidak dapat mengubah Super Admin'
                )

                ->body(
                    'Super Admin tidak dapat dikelola melalui halaman ini.'
                )

                ->danger()

                ->send();

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | ROLE
        |--------------------------------------------------------------------------
        */

        $role =
            collect(
                $this->selectedRole
            )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        */

        if (
            $role === 'super_admin'
            || blank($role)
        ) {

            $role = 'user';
        }


        /*
        |--------------------------------------------------------------------------
        | SYNC ROLE
        |--------------------------------------------------------------------------
        */

        $this->record->syncRoles([

            $role,

        ]);


        /*
        |--------------------------------------------------------------------------
        | SYNC DIRECT PERMISSIONS
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
        | DATA USER BARU
        |--------------------------------------------------------------------------
        */

        $newNIK =
            $this->record->NIK;


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA KARYAWAN TERBARU
        |--------------------------------------------------------------------------
        */

        $karyawan =
            MstKaryawan::query()
                ->with([
                    'kepalaBagian',
                    'kepalaBagian.user',
                    'departemen',
                    'perusahaan',
                ])
                ->where(
                    'NIK',
                    $newNIK
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | DATA KEPALA BAGIAN BARU
        |--------------------------------------------------------------------------
        */

        $newKepalaBagianNIK =
            $karyawan
                ?->NIKKepalaBagian;


        $newKepalaBagianName =
            $karyawan
                ?->kepalaBagian
                ?->Nama;


        $newKepalaBagianEmail =
            $karyawan
                ?->kepalaBagian
                ?->user
                ?->email;


        /*
        |--------------------------------------------------------------------------
        | OLD / NEW ROLE
        |--------------------------------------------------------------------------
        */

        $oldRole =
            $this->oldRole;


        $newRole =
            $role;


        /*
        |--------------------------------------------------------------------------
        | OLD / NEW PERMISSION
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


        /*
        |--------------------------------------------------------------------------
        | ADDED PERMISSION
        |--------------------------------------------------------------------------
        */

        $added =
            $newPermissions
                ->diff(
                    $oldPermissions
                )
                ->values()
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | REMOVED PERMISSION
        |--------------------------------------------------------------------------
        */

        $removed =
            $oldPermissions
                ->diff(
                    $newPermissions
                )
                ->values()
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | CEK PERUBAHAN NIK
        |--------------------------------------------------------------------------
        */

        $nikChanged =
            $this->oldNIK !== $newNIK;


        /*
        |--------------------------------------------------------------------------
        | CEK PERUBAHAN KEPALA BAGIAN
        |--------------------------------------------------------------------------
        */

        $kepalaBagianChanged =
            $this->oldKepalaBagianNIK
            !==
            $newKepalaBagianNIK;


        /*
        |--------------------------------------------------------------------------
        | ROLE CHANGED
        |--------------------------------------------------------------------------
        */

        $roleChanged =
            $oldRole !== $newRole;


        /*
        |--------------------------------------------------------------------------
        | PERMISSION CHANGED
        |--------------------------------------------------------------------------
        */

        $permissionChanged =
            count($added) > 0
            ||
            count($removed) > 0;


        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOG
        |--------------------------------------------------------------------------
        */

        if (
            $nikChanged
            ||
            $kepalaBagianChanged
            ||
            $roleChanged
            ||
            $permissionChanged
        ) {

            activity('user_management')

                ->causedBy(
                    auth()->user()
                )

                ->performedOn(
                    $this->record
                )

                ->withProperties([

                    /*
                    |--------------------------------------------------------------------------
                    | USER
                    |--------------------------------------------------------------------------
                    */

                    'user_id' =>
                        $this->record->id,

                    'user_name' =>
                        $this->record->name,

                    'user_email' =>
                        $this->record->email,


                    /*
                    |--------------------------------------------------------------------------
                    | NIK
                    |--------------------------------------------------------------------------
                    */

                    'old_nik' =>
                        $this->oldNIK,

                    'new_nik' =>
                        $newNIK,


                    /*
                    |--------------------------------------------------------------------------
                    | KEPALA BAGIAN
                    |--------------------------------------------------------------------------
                    */

                    'old_kepala_bagian_nik' =>
                        $this->oldKepalaBagianNIK,

                    'new_kepala_bagian_nik' =>
                        $newKepalaBagianNIK,

                    'old_kepala_bagian_name' =>
                        $this->oldKepalaBagianName,

                    'new_kepala_bagian_name' =>
                        $newKepalaBagianName,

                    'old_kepala_bagian_email' =>
                        $this->oldKepalaBagianEmail,

                    'new_kepala_bagian_email' =>
                        $newKepalaBagianEmail,


                    /*
                    |--------------------------------------------------------------------------
                    | ROLE
                    |--------------------------------------------------------------------------
                    */

                    'old_role' =>
                        $oldRole,

                    'new_role' =>
                        $newRole,


                    /*
                    |--------------------------------------------------------------------------
                    | PERMISSION
                    |--------------------------------------------------------------------------
                    */

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


                    /*
                    |--------------------------------------------------------------------------
                    | MASTER KARYAWAN
                    |--------------------------------------------------------------------------
                    */

                    'departemen' =>
                        $karyawan
                            ?->departemen
                            ?->NamaDept,

                    'perusahaan' =>
                        $karyawan
                            ?->perusahaan
                            ?->NamaPerusahaan,


                    /*
                    |--------------------------------------------------------------------------
                    | REQUEST
                    |--------------------------------------------------------------------------
                    */

                    'ip_address' =>
                        request()->ip(),

                    'user_agent' =>
                        request()->userAgent(),

                ])

                ->log(
                    'Data user, Kepala Bagian, role dan permission diperbarui'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | NOTIFICATION
        |--------------------------------------------------------------------------
        */

        $kepalaBagianText =
            $newKepalaBagianName
            ?? 'Belum ditentukan';


        Notification::make()

            ->title(
                'User berhasil diperbarui'
            )

            ->body(
                'Data user berhasil diperbarui. Kepala Bagian: '
                . $kepalaBagianText
                . '.'
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
        return 'Edit User: ' .
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
