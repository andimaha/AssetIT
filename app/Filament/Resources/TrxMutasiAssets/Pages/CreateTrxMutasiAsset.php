<?php

namespace App\Filament\Resources\TrxMutasiAssets\Pages;

use App\Filament\Resources\TrxMutasiAssets\TrxMutasiAssetResource;
use Filament\Resources\Pages\CreateRecord;


class CreateTrxMutasiAsset extends CreateRecord
{
    protected static string $resource = TrxMutasiAssetResource::class;


    protected function afterCreate(): void
    {
        $record = $this->record;


        $asset = $record->asset;


        if (!$asset) {
            return;
        }


        $lastMutation = $asset
            ->mutasiAsset()
            ->orderByDesc('TanggalMutasi')
            ->first();


        if ($lastMutation) {

            $asset->update([
                'NIK' => $lastMutation->NIK,
                'IDLokasi' => $lastMutation->IDLokasi,
            ]);

        }
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
