<?php

namespace App\Filament\Resources\RoleManagements\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

use Spatie\Permission\Models\Permission;


class RoleManagementForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | INFORMASI ROLE
            |--------------------------------------------------------------------------
            */

            Section::make(
                'Informasi Role'
            )
                ->description(
                    'Tentukan nama role dan tingkat akses yang akan dimiliki.'
                )
                ->schema([

                    TextInput::make(
                        'name'
                    )
                        ->label(
                            'Nama Role'
                        )
                        ->required()
                        ->maxLength(255)
                        ->unique(
                            table: 'roles',
                            column: 'name',
                            ignoreRecord: true
                        )
                        ->helperText(
                            'Gunakan nama role yang jelas, misalnya staff_it atau kepala_bagian.'
                        )
                        ->disabled(
                            fn (
                                ?object $record
                            ): bool =>
                                $record?->name === 'super_admin'
                        )
                        ->dehydrated(),

                ])
                ->columns(1)
                ->columnSpanFull(),


            /*
            |--------------------------------------------------------------------------
            | MASTER DATA
            |--------------------------------------------------------------------------
            */

            Section::make(
                'Master Data'
            )
                ->description(
                    'Permission untuk mengakses dan mengelola data utama sistem.'
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
                    'Permission untuk menjalankan dan mengelola transaksi sistem.'
                )
                ->schema([

                    self::permissionList(
                        'trx'
                    ),

                ])
                ->columnSpanFull(),


            /*
            |--------------------------------------------------------------------------
            | IT REQUEST
            |--------------------------------------------------------------------------
            */

            Section::make(
                'IT Request'
            )
                ->description(
                    'Permission untuk Permintaan IT dan proses approval Kepala Bagian.'
                )
                ->schema([

                    self::permissionList(
                        'itrequest'
                    ),

                ])
                ->columnSpanFull(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PERMISSION LIST
    |--------------------------------------------------------------------------
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

            ->options(
                fn (): array =>
                    Permission::query()
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

                                        'approval.view' =>
                                            1,

                                        'approval.approve' =>
                                            2,

                                        'approval.reject' =>
                                            3,

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
                                | RESOURCE LABEL
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
                                | ACTION LABEL
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

                                        'approval.view' =>
                                            'Approval — Read',

                                        'approval.approve' =>
                                            'Approval — Approve',

                                        'approval.reject' =>
                                            'Approval — Reject',

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
                                | KHUSUS IT REQUEST
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    $resource === 'itrequest'
                                    && str_starts_with(
                                        $permission->name,
                                        'itrequest.approval.'
                                    )
                                ) {

                                    $resourceLabel =
                                        'IT Request';

                                }


                                return [

                                    $permission->name =>
                                        "{$resourceLabel} — {$actionLabel}",

                                ];
                            }
                        )
                        ->toArray()
            )

            ->columns(
                4
            )

            ->gridDirection(
                'row'
            )

            ->searchable()

            ->bulkToggleable()

            ->helperText(
                'Pilih permission yang akan dimiliki oleh role ini.'
            );
    }
}
