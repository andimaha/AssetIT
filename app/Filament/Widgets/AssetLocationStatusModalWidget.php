<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AssetLocationStatusModalWidget extends Widget
{
    protected string $view =
        'filament.widgets.asset-location-status-modal-widget';

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 99;
}