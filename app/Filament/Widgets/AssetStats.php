<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use App\Models\TrxServiceAsset;
use App\Models\TrxRetireAsset;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


class AssetStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make(
                'Total Asset',
                MstAsset::count()
            )
            ->description('Total asset terdaftar')
            ->color('primary'),


            Stat::make(
                'Asset Not Used',
                MstAsset::where(
                    'StatusAsset',
                    'Not Used'
                )->count()
            )
            ->description('Asset tidak digunakan')
            ->color('success'),


            Stat::make(
                'Asset Service',
                TrxServiceAsset::where(
                    'StatusService',
                    'Proses'
                )->count()
            )
            ->description('Asset sedang diperbaiki')
            ->color('warning'),


            Stat::make(
                'Asset Retired',
                MstAsset::where(
                    'StatusAsset',
                    'Retired'
                )->count()
            )
            ->description('Asset Retired')
            ->color('danger'),

        ];
    }
}