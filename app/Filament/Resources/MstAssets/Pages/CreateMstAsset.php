<?php

namespace App\Filament\Resources\MstAssets\Pages;

use App\Filament\Resources\MstAssets\MstAssetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMstAsset extends CreateRecord
{
    protected static string $resource = MstAssetResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (
            !isset($data['Garansi'])
            || $data['Garansi'] === null
            || $data['Garansi'] === ''
        ) {
            $data['Garansi'] = 0;
        }


        return $data;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}