<?php

namespace App\Filament\Resources\TrxMutasiAssets\Pages;

use App\Filament\Resources\TrxMutasiAssets\TrxMutasiAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;


class EditTrxMutasiAsset extends EditRecord
{
    protected static string $resource = TrxMutasiAssetResource::class;


    protected function afterSave(): void
    {
        $this->updateLatestMutation();
    }


    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make()
                ->after(function () {
                    $this->updateLatestMutation();
                }),

        ];
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    private function updateLatestMutation(): void
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

        } else {

            $asset->update([
                'NIK' => null,
                'IDLokasi' => null,
            ]);

        }
    }
}
