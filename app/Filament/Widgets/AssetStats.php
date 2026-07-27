<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use App\Models\TrxServiceAsset;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


class AssetStats extends StatsOverviewWidget
{


    public bool $showModal = false;


    public string $modalTitle = '';


    public $modalData = [];





    protected function getStats(): array
    {
        return [

            Stat::make(
                'Total Asset',
                MstAsset::count()
            )
            ->description('Klik untuk detail')
            ->color('primary')
            ->extraAttributes([
                'wire:click' => "showAssetDetail('total')",
                'class' => 'cursor-pointer',
            ]),



            Stat::make(
                'Asset Not Used',
                MstAsset::where(
                    'StatusAsset',
                    'Not Used'
                )->count()
            )
            ->description('Klik untuk detail')
            ->color('success')
            ->extraAttributes([
                'wire:click' => "showAssetDetail('not_used')",
                'class' => 'cursor-pointer',
            ]),



            Stat::make(
                'Asset Service',
                TrxServiceAsset::where(
                    'StatusService',
                    'Proses'
                )->count()
            )
            ->description('Sedang diperbaiki')
            ->color('warning')
            ->extraAttributes([
                'wire:click' => "showAssetDetail('service')",
                'class' => 'cursor-pointer',
            ]),



            Stat::make(
                'Asset Retired',
                MstAsset::where(
                    'StatusAsset',
                    'Retired'
                )->count()
            )
            ->description('Klik untuk detail')
            ->color('danger')
            ->extraAttributes([
                'wire:click' => "showAssetDetail('retired')",
                'class' => 'cursor-pointer',
            ]),

        ];
    }





    public function showAssetDetail(string $type): void
    {

        $this->showModal = true;


        switch ($type) {


            case 'total':

                $this->modalTitle = 'Total Asset';

                $this->modalData =
                    MstAsset::with([
                        'karyawan',
                        'perusahaan'
                    ])
                    ->get();

            break;



            case 'not_used':

                $this->modalTitle = 'Asset Not Used';

                $this->modalData =
                    MstAsset::with([
                        'karyawan',
                        'perusahaan'
                    ])
                    ->where(
                        'StatusAsset',
                        'Not Used'
                    )
                    ->get();

            break;



            case 'retired':

                $this->modalTitle = 'Asset Retired';

                $this->modalData =
                    MstAsset::with([
                        'karyawan',
                        'perusahaan'
                    ])
                    ->where(
                        'StatusAsset',
                        'Retired'
                    )
                    ->get();

            break;



            case 'service':

                $this->modalTitle = 'Asset Service';

                $this->modalData =
                    TrxServiceAsset::with('asset')
                    ->where(
                        'StatusService',
                        'Proses'
                    )
                    ->get();

            break;

        }

    }





    public function closeModal(): void
    {
        $this->showModal = false;
    }


}