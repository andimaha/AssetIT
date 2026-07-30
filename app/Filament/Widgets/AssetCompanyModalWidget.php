<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AssetCompanyModalWidget extends Widget
{

    protected string $view = 'filament.widgets.asset-company-modal-widget';


    protected static bool $isDiscovered = false;


    protected static ?int $sort = 99;

}