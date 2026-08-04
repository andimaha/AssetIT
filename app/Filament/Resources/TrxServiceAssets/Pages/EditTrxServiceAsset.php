<?php

namespace App\Filament\Resources\TrxServiceAssets\Pages;

use App\Filament\Resources\TrxServiceAssets\TrxServiceAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrxServiceAsset extends EditRecord
{
    protected static string $resource = TrxServiceAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
