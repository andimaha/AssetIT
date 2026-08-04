<?php

namespace App\Filament\Resources\TrxServiceAssets\Pages;

use App\Filament\Resources\TrxServiceAssets\TrxServiceAssetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrxServiceAsset extends CreateRecord
{
    protected static string $resource = TrxServiceAssetResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}