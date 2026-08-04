<?php

namespace App\Filament\Resources\TrxServiceAssets\Pages;

use App\Filament\Resources\TrxServiceAssets\TrxServiceAssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrxServiceAssets extends ListRecords
{
    protected static string $resource = TrxServiceAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
