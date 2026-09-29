<?php

namespace App\Filament\Resources\UserManagements\Schemas;

use App\Models\MstKaryawan;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

use Illuminate\Validation\Rules\Password;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class UserManagementForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | INFORMASI AKUN
            |--------------------------------------------------------------------------
            */

            Section::make(
                'Informasi Akun'
            )
                ->description(
                    'Informasi dasar akun pengguna.'
                )
                ->schema([

                    /*
                    |--------------------------------------------------------------------------
                    | NIK KARYAWAN
                    |--------------------------------------------------------------------------
                    */

                    Select::make(
                        'NIK'
                    )
                        ->label(
                            'Karyawan'
                        )
                        ->options(
                            fn (): array =>
                                MstKaryawan::query()
                                    ->orderBy(
                                        'Nama'
                                    )
                                    ->get()
                                    ->mapWithKeys(
                                        function (
                                            MstKaryawan $karyawan
                                        ): array {

                                            return [

                                                $karyawan->NIK =>
                                                    $karyawan->NIK
                                                    . ' - '
                                                    . $karyawan->Nama,

                                            ];

                                        }
                                    )
                                    ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->unique(
                            table: 'users',
                            column: 'NIK',
                            ignoreRecord: true
                        )
                        ->live()
                        ->afterStateUpdated(
                            function (
                                $state,
                                callable $set
                            ): void {

                                if (
                                    blank($state)
                                ) {

                                    return;
                                }

                                $karyawan =
                                    MstKaryawan::query()
                                        ->where(
                                            'NIK',
                                            $state
                                        )
                                        ->first();

                                if (
                                    $karyawan
                                ) {

                                    $set(
                                        'name',
                                        $karyawan->Nama
                                    );

                                }

                            }
                        )
                        ->helperText(
                            'Pilih karyawan berdasarkan NIK dan nama. Data diambil dari Master Karyawan.'
                        )
                        ->columnSpanFull(),


                    /*
                    |--------------------------------------------------------------------------
                    | NAMA
                    |--------------------------------------------------------------------------
                    */

                    TextInput::make(
                        'name'
                    )
                        ->label(
                            'Nama'
                        )
                        ->required()
                        ->maxLength(255)
                        ->autofocus()
                        ->helperText(
                            'Nama otomatis mengikuti Master Karyawan setelah memilih karyawan.'
                        ),


                    /*
                    |--------------------------------------------------------------------------
                    | EMAIL
                    |--------------------------------------------------------------------------
                    */

                    TextInput::make(
                        'email'
                    )
                        ->label(
                            'Email'
                        )
                        ->email()
                        ->required()
                        ->unique(
                            table: 'users',
                            column: 'email',
                            ignoreRecord: true
                        )
                        ->maxLength(255),


                    /*
                    |--------------------------------------------------------------------------
                    | PASSWORD
                    |--------------------------------------------------------------------------
                    */

                    TextInput::make(
                        'password'
                    )
                        ->label(
                            'Password'
                        )
                        ->password()
                        ->revealable()
                        ->required(
                            fn (
                                string $operation
                            ): bool =>
                                $operation === 'create'
                        )
                        ->rule(
                            Password::defaults()
                        )
                        ->dehydrateStateUsing(
                            fn (
                                ?string $state
                            ): ?string =>
                                filled($state)
                                    ? bcrypt($state)
                                    : null
                        )
                        ->dehydrated(
                            fn (
                                ?string $state
                            ): bool =>
                                filled($state)
                        )
                        ->helperText(
                            fn (
                                string $operation
                            ): string =>
                                $operation === 'create'
                                    ? 'Password wajib diisi.'
                                    : 'Kosongkan jika password tidak ingin diubah.'
                        ),

                ])
                ->columns(2)
                ->columnSpanFull(),


            /*
            |--------------------------------------------------------------------------
            | KEPALA BAGIAN
            |--------------------------------------------------------------------------
            */

            Section::make(
                'Kepala Bagian'
            )
                ->description(
                    'Kepala Bagian mengikuti struktur organisasi pada Master Karyawan dan tidak dapat diubah dari User Management.'
                )
                ->schema([

                    TextInput::make(
                        'kepala_bagian_display'
                    )
                        ->label(
                            'Kepala Bagian'
                        )
                        ->readOnly()
                        ->formatStateUsing(
                            function (
                                $state,
                                $record
                            ): string {

                                $nik =
                                    $record?->NIK
                                    ?? null;

                                if (
                                    blank($nik)
                                ) {

                                    return '-';
                                }

                                $karyawan =
                                    MstKaryawan::query()
                                        ->with([
                                            'kepalaBagian',
                                            'kepalaBagian.departemen',
                                        ])
                                        ->where(
                                            'NIK',
                                            $nik
                                        )
                                        ->first();

                                if (
                                    ! $karyawan
                                ) {

                                    return '-';
                                }

                                $kepalaBagian =
                                    $karyawan
                                        ->kepalaBagian;

                                if (
                                    ! $kepalaBagian
                                ) {

                                    return 'Tidak ada Kepala Bagian';
                                }

                                $departemen =
                                    $kepalaBagian
                                        ->departemen
                                        ?->NamaDept
                                    ?? '-';

                                return
                                    $kepalaBagian->NIK
                                    . ' | '
                                    . (
                                        $kepalaBagian->Nama
                                        ?? '-'
                                    )
                                    . ' | '
                                    . $departemen;

                            }
                        )
                        ->helperText(
                            'Kepala Bagian ditentukan dari kolom NIKKepalaBagian pada Master Karyawan. Untuk mengubahnya, edit data karyawan pada Master Karyawan.'
                        )
                        ->dehydrated(
                            false
                        )
                        ->columnSpanFull(),

                ])
                ->columnSpanFull(),


            /*
            |--------------------------------------------------------------------------
            | ROLE
            |--------------------------------------------------------------------------
            */

            Section::make(
                'Role Pengguna'
            )
                ->description(
                    'Tentukan jenis pengguna dan tingkat aksesnya.'
                )
                ->schema([

                    Select::make(
                        'role'
                    )
                        ->label(
                            'Role'
                        )
                        ->options(
                            fn (): array =>
                                Role::query()
                                    ->where(
                                        'guard_name',
                                        'web'
                                    )
                                    ->where(
                                        'name',
                                        '!=',
                                        'super_admin'
                                    )
                                    ->orderBy(
                                        'name'
                                    )
                                    ->pluck(
                                        'name',
                                        'name'
                                    )
                                    ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->helperText(
                            'Super Admin dikelola secara khusus dan tidak dapat diberikan melalui form ini.'
                        ),

                ])
                ->columnSpanFull(),


            /*
            |--------------------------------------------------------------------------
            | HAK AKSES
            |--------------------------------------------------------------------------
            */

            Section::make(
                'Hak Akses'
            )
                ->description(
                    'Pilih hak akses pengguna berdasarkan bagian sistem yang dapat digunakan.'
                )
                ->schema([

                    /*
                    |--------------------------------------------------------------------------
                    | MASTER DATA
                    |--------------------------------------------------------------------------
                    */

                    Section::make(
                        'Master Data'
                    )
                        ->description(
                            'Hak akses untuk mengelola data utama sistem.'
                        )
                        ->schema([

                            self::permissionList(
                                'mst'
                            ),

                        ])
                        ->columnSpanFull(),


                    /*
                    |--------------------------------------------------------------------------
                    | TRANSAKSI
                    |--------------------------------------------------------------------------
                    */

                    Section::make(
                        'Transaksi'
                    )
                        ->description(
                            'Hak akses untuk menjalankan dan mengelola transaksi sistem.'
                        )
                        ->schema([

                            self::permissionList(
                                'trx'
                            ),

                            self::permissionList(
                                'itrequest'
                            ),

                        ])
                        ->columnSpanFull(),

                ])
                ->columnSpanFull(),

        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | PERMISSION LIST
    |--------------------------------------------------------------------------
    |
    | Nama state:
    |
    | permissions_mst
    | permissions_trx
    | permissions_itrequest
    |
    | State hanya menyimpan DIRECT PERMISSION user.
    |
    | Permission yang berasal dari Role:
    |
    | - tetap ditampilkan
    | - diberi tanda [ROLE]
    | - checkbox tidak dapat dimatikan
    | - tidak dimasukkan ke direct permission
    |
    */

    protected static function permissionList(
        string $prefix
    ): CheckboxList {

        return CheckboxList::make(
            'permissions_' . $prefix
        )

            ->label(
                false
            )

            /*
            |--------------------------------------------------------------------------
            | OPTIONS
            |--------------------------------------------------------------------------
            */

            ->options(
                function (
                    callable $get
                ) use (
                    $prefix
                ): array {

                    /*
                    |--------------------------------------------------------------------------
                    | ROLE AKTIF
                    |--------------------------------------------------------------------------
                    */

                    $roleName =
                        $get(
                            'role'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | PERMISSION DARI ROLE
                    |--------------------------------------------------------------------------
                    */

                    $rolePermissions =
                        collect();


                    if (
                        filled($roleName)
                    ) {

                        $role =
                            Role::query()
                                ->where(
                                    'guard_name',
                                    'web'
                                )
                                ->where(
                                    'name',
                                    $roleName
                                )
                                ->first();

                        if (
                            $role
                        ) {

                            $rolePermissions =
                                $role
                                    ->permissions()
                                    ->pluck(
                                        'name'
                                    )
                                    ->unique();

                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SEMUA PERMISSION
                    |--------------------------------------------------------------------------
                    */

                    return Permission::query()
                        ->where(
                            'guard_name',
                            'web'
                        )
                        ->where(
                            'name',
                            'like',
                            "{$prefix}%"
                        )
                        ->get()

                        ->sortBy(
                            function (
                                Permission $permission
                            ): array {

                                $parts =
                                    explode(
                                        '.',
                                        $permission->name,
                                        2
                                    );

                                $resource =
                                    $parts[0]
                                    ?? $permission->name;

                                $action =
                                    $parts[1]
                                    ?? '';

                                $actionOrder =
                                    match (
                                        $action
                                    ) {

                                        'create' =>
                                            1,

                                        'view' =>
                                            2,

                                        'update' =>
                                            3,

                                        'delete' =>
                                            4,

                                        default =>
                                            99,

                                    };

                                return [

                                    $resource,

                                    $actionOrder,

                                ];

                            }
                        )

                        ->mapWithKeys(
                            function (
                                Permission $permission
                            ) use (
                                $rolePermissions
                            ): array {

                                $parts =
                                    explode(
                                        '.',
                                        $permission->name,
                                        2
                                    );

                                $resource =
                                    $parts[0]
                                    ?? $permission->name;

                                $action =
                                    $parts[1]
                                    ?? null;


                                /*
                                |--------------------------------------------------------------------------
                                | NAMA MODUL
                                |--------------------------------------------------------------------------
                                */

                                $resourceLabel =
                                    str($resource)

                                        ->replaceFirst(
                                            'mst',
                                            ''
                                        )

                                        ->replaceFirst(
                                            'trx',
                                            ''
                                        )

                                        ->replaceFirst(
                                            'itrequest',
                                            'IT Request'
                                        )

                                        ->replace(
                                            [
                                                '_',
                                                '-',
                                            ],
                                            ' '
                                        )

                                        ->title()

                                        ->toString();


                                /*
                                |--------------------------------------------------------------------------
                                | NAMA ACTION
                                |--------------------------------------------------------------------------
                                */

                                $actionLabel =
                                    match (
                                        $action
                                    ) {

                                        'create' =>
                                            'Create',

                                        'view' =>
                                            'Read',

                                        'update' =>
                                            'Update',

                                        'delete' =>
                                            'Delete',

                                        default =>
                                            str(
                                                $action ?? ''
                                            )
                                                ->replace(
                                                    [
                                                        '_',
                                                        '-',
                                                    ],
                                                    ' '
                                                )
                                                ->title()
                                                ->toString(),

                                    };


                                /*
                                |--------------------------------------------------------------------------
                                | CEK PERMISSION DARI ROLE
                                |--------------------------------------------------------------------------
                                */

                                $isRolePermission =
                                    $rolePermissions
                                        ->contains(
                                            $permission->name
                                        );


                                /*
                                |--------------------------------------------------------------------------
                                | LABEL
                                |--------------------------------------------------------------------------
                                */

                                $label =
                                    "{$resourceLabel} — {$actionLabel}";


                                if (
                                    $isRolePermission
                                ) {

                                    $label .=
                                        ' [ROLE]';

                                }


                                return [

                                    $permission->name =>
                                        $label,

                                ];

                            }
                        )

                        ->toArray();

                }
            )

            /*
            |--------------------------------------------------------------------------
            | DISABLE PERMISSION YANG BERASAL DARI ROLE
            |--------------------------------------------------------------------------
            |
            | Ini bagian penting.
            |
            | Permission [ROLE] tidak boleh dimatikan dari User Management.
            |
            | Contoh:
            |
            | Role:
            |   staff_it
            |
            | Memiliki:
            |   itrequest.create
            |   itrequest.view
            |
            | Maka:
            |
            |   IT Request — Create [ROLE]  ☑ disabled
            |   IT Request — Read   [ROLE]  ☑ disabled
            |
            | Sedangkan permission direct tetap editable.
            |
            */

            ->disableOptionWhen(
                function (
                    string|int $value,
                    callable $get
                ) use (
                    $prefix
                ): bool {

                    $roleName =
                        $get(
                            'role'
                        );

                    if (
                        blank($roleName)
                    ) {

                        return false;
                    }

                    $role =
                        Role::query()
                            ->where(
                                'guard_name',
                                'web'
                            )
                            ->where(
                                'name',
                                $roleName
                            )
                            ->first();

                    if (
                        ! $role
                    ) {

                        return false;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CARI PERMISSION
                    |--------------------------------------------------------------------------
                    */

                    $permissionName =
                        (string) $value;

                    /*
                    |--------------------------------------------------------------------------
                    | PASTIKAN PERMISSION BERASAL DARI ROLE
                    |--------------------------------------------------------------------------
                    */

                    return $role
                        ->permissions()
                        ->where(
                            'name',
                            $permissionName
                        )
                        ->exists();

                }
            )

            /*
            |--------------------------------------------------------------------------
            | STATE
            |--------------------------------------------------------------------------
            |
            | CheckboxList hanya menyimpan DIRECT PERMISSION.
            |
            | Permission dari Role tidak perlu dimasukkan ke state
            | karena akses tersebut sudah diberikan oleh Role.
            |
            */

            ->columns(
                4
            )

            ->gridDirection(
                'row'
            )

            ->searchable()

            ->bulkToggleable()

            ->helperText(
                'Permission dengan tanda [ROLE] berasal dari Role dan tidak dapat dimatikan dari User Management. Permission lainnya adalah direct permission pengguna.'
            );

    }
}
