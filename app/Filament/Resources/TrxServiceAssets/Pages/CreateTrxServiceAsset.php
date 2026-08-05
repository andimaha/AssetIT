<?php

namespace App\Filament\Resources\TrxServiceAssets\Pages;

use App\Filament\Resources\TrxServiceAssets\TrxServiceAssetResource;
use App\Models\MstAsset;

use Filament\Resources\Pages\CreateRecord;


class CreateTrxServiceAsset extends CreateRecord
{

    protected static string $resource = TrxServiceAssetResource::class;



    protected function afterCreate(): void
    {
        $this->updateAssetStatus();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }



    protected function updateAssetStatus(): void
    {

        $service = $this->record;


        $asset = MstAsset::where(
            'NoAssetIT',
            $service->NoAssetIT
        )->first();



        if(!$asset){
            return;
        }



        $services = $asset
            ->service()
            ->get();



        if(
            $services
                ->where('StatusService','Proses')
                ->isNotEmpty()
        ){

            $status = 'In Service';

        }


        elseif(
            $services
                ->where('StatusService','Unrepairable')
                ->isNotEmpty()
        ){

            $status = 'Retired';

        }


        else{

            $status = 'Available';

        }



        $asset->update([

            'StatusAsset'=>$status

        ]);

    }


}