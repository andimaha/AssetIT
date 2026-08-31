<?php

namespace App\Filament\Resources\TrxRetireAssets\Pages;

use App\Filament\Resources\TrxRetireAssets\TrxRetireAssetResource;
use Filament\Resources\Pages\CreateRecord;


class CreateTrxRetireAsset extends CreateRecord
{
    protected static string $resource = TrxRetireAssetResource::class;


    protected function afterCreate(): void
    {
        $this->record->asset?->update([
            'StatusAsset' => 'Retired',
        ]);
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
