<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SoftwareAssignmentCompanyModalWidget extends Widget
{
    protected string $view =
        'filament.widgets.software-assignment-company-modal-widget';

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 99;
}
