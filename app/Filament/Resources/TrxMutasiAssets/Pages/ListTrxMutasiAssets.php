<?php

namespace App\Filament\Resources\TrxMutasiAssets\Pages;

use App\Filament\Resources\TrxMutasiAssets\TrxMutasiAssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;


class ListTrxMutasiAssets extends ListRecords
{
    protected static string $resource = TrxMutasiAssetResource::class;


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
