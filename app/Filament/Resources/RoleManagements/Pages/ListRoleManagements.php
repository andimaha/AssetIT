<?php

namespace App\Filament\Resources\RoleManagements\Pages;

use App\Filament\Resources\RoleManagements\RoleManagementResource;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;


class ListRoleManagements extends ListRecords
{
    protected static string $resource =
        RoleManagementResource::class;


    /*
    |--------------------------------------------------------------------------
    | HEADER ACTIONS
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()

                ->label(
                    'Tambah Role'
                )

                ->visible(
                    fn (): bool =>
                        RoleManagementResource::canCreate()
                ),

        ];
    }
}
