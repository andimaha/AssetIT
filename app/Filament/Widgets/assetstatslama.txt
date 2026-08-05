<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use App\Models\TrxServiceAsset;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStats extends StatsOverviewWidget
{
    /**
     * Tampilkan 5 widget dalam 1 baris.
     */
    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
    {
        return [

            Stat::make(
                'Total',
                MstAsset::count()
            )
                ->description('Semua Asset')
                ->color('primary'),

            Stat::make(
                'Available',
                MstAsset::where('StatusAsset', 'Available')->count()
            )
                ->description('Tersedia')
                ->color('info'),

            Stat::make(
                'Not Used',
                MstAsset::where('StatusAsset', 'Not Used')->count()
            )
                ->description('Tidak Dipakai')
                ->color('success'),

            Stat::make(
                'Service',
                TrxServiceAsset::where('StatusService', 'Proses')->count()
            )
                ->description('Dalam Service')
                ->color('warning'),

            Stat::make(
                'Retired',
                MstAsset::where('StatusAsset', 'Retired')->count()
            )
                ->description('Pensiun')
                ->color('danger'),

        ];
    }
}