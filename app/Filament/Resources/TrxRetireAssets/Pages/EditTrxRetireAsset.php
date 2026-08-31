<?php

namespace App\Filament\Resources\TrxRetireAssets\Pages;

use App\Filament\Resources\TrxRetireAssets\TrxRetireAssetResource;
use Filament\Resources\Pages\EditRecord;


class EditTrxRetireAsset extends EditRecord
{
    protected static string $resource = TrxRetireAssetResource::class;


    protected function afterSave(): void
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
