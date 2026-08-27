<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AssetDepartmentModalWidget extends Widget
{
    protected string $view =
        'filament.widgets.asset-department-modal-widget';

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 99;
}
