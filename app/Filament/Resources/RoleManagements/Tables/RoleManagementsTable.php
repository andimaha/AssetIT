<?php

namespace App\Filament\Resources\RoleManagements\Tables;

use App\Filament\Resources\RoleManagements\RoleManagementResource;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;


class RoleManagementsTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            /*
            |--------------------------------------------------------------------------
            | QUERY
            |--------------------------------------------------------------------------
            */

            ->modifyQueryUsing(
                function (
                    Builder $query
                ) {

                    $query->with([
                        'permissions',
                        'users',
                    ]);

                }
            )


            /*
            |--------------------------------------------------------------------------
            | COLUMNS
            |--------------------------------------------------------------------------
            */

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | ROLE
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'name'
                )
                    ->label(
                        'ROLE'
                    )
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(
                        fn (
                            string $state
                        ): string =>
                            match ($state) {

                                'super_admin' =>
                                    'danger',

                                'staff_it' =>
                                    'warning',

                                'kepala_bagian' =>
                                    'info',

                                'user' =>
                                    'gray',

                                default =>
                                    'primary',

                            }
                    ),


                /*
                |--------------------------------------------------------------------------
                | PERMISSION
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'permissions_count'
                )
                    ->label(
                        'PERMISSION'
                    )
                    ->counts(
                        'permissions'
                    )
                    ->badge()
                    ->color(
                        'success'
                    )
                    ->sortable(),


                /*
                |--------------------------------------------------------------------------
                | USERS
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'users_count'
                )
                    ->label(
                        'PENGGUNA'
                    )
                    ->counts(
                        'users'
                    )
                    ->badge()
                    ->color(
                        'primary'
                    )
                    ->sortable(),


                /*
                |--------------------------------------------------------------------------
                | CREATED AT
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'created_at'
                )
                    ->label(
                        'DIBUAT'
                    )
                    ->dateTime(
                        'd M Y H:i'
                    )
                    ->sortable(),


                /*
                |--------------------------------------------------------------------------
                | UPDATED AT
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'updated_at'
                )
                    ->label(
                        'TERAKHIR DIUBAH'
                    )
                    ->dateTime(
                        'd M Y H:i'
                    )
                    ->sortable(),

            ])


            /*
            |--------------------------------------------------------------------------
            | RECORD ACTIONS
            |--------------------------------------------------------------------------
            */

            ->recordActions([

                /*
                |--------------------------------------------------------------------------
                | EDIT
                |--------------------------------------------------------------------------
                */

                EditAction::make()

                    ->label(
                        'Edit'
                    )

                    ->visible(
                        fn (
                            $record
                        ): bool =>
                            RoleManagementResource::canEdit(
                                $record
                            )
                    ),


                /*
                |--------------------------------------------------------------------------
                | DELETE
                |--------------------------------------------------------------------------
                */

                DeleteAction::make()

                    ->label(
                        'Hapus'
                    )

                    ->visible(
                        fn (
                            $record
                        ): bool =>
                            RoleManagementResource::canDelete(
                                $record
                            )
                    ),

            ])


            /*
            |--------------------------------------------------------------------------
            | EMPTY STATE
            |--------------------------------------------------------------------------
            */

            ->emptyStateHeading(
                'Belum ada role'
            )

            ->emptyStateDescription(
                'Belum ada role yang tersedia.'
            );

    }
}
