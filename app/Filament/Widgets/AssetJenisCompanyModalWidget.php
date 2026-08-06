<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AssetJenisCompanyModalWidget extends Widget
{
    protected string $view = 'filament.widgets.asset-jenis-company-modal-widget';

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 99;
}