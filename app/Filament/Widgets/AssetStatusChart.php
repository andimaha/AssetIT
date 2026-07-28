<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use App\Models\MstPerusahaan;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class AssetStatusChart extends ChartWidget
{
    protected ?string $heading = 'Asset Berdasarkan Status';


    // protected static ?int $sort = 2;



    public ?string $filter = 'all';




    protected function getFilters(): ?array
    {
        return [

            'all' => 'Semua Perusahaan',

        ] + MstPerusahaan::query()

            ->orderBy('NamaPerusahaan')

            ->pluck(
                'NamaPerusahaan',
                'IDPerusahaan'
            )

            ->toArray();
    }





    protected function getData(): array
    {

        $query = MstAsset::query();



        if (
            $this->filter !== null
            &&
            $this->filter !== 'all'
        ) {

            $query->where(
                'IDPerusahaan',
                $this->filter
            );

        }



        $statuses = [

            'Available',

            'In Service',

            'Not Used',

            'Retired',

        ];




        $result = $query

            ->selectRaw(
                'StatusAsset, COUNT(*) as total'
            )

            ->groupBy('StatusAsset')

            ->pluck(
                'total',
                'StatusAsset'
            );






        return [

            'datasets' => [

                [

                    'label' => 'Jumlah Asset',


                    'data' => collect($statuses)

                        ->map(
                            fn ($status) =>
                            $result[$status] ?? 0
                        )

                        ->toArray(),




                    'backgroundColor' => [

                        '#10B981',

                        '#F59E0B',

                        '#3B82F6',

                        '#EF4444',

                    ],



                    'borderColor' => '#FFFFFF',


                    'borderWidth' => 2,


                    'hoverOffset' => 12,


                ],

            ],



            'labels' => $statuses,

        ];

    }






    protected function getType(): string
    {
        return 'pie';
    }







    protected function getOptions(): RawJs
{
    return RawJs::make(<<<'JS'

{
    onClick(event, elements, chart)
    {
        if (!elements.length) {
            return;
        }


        const index = elements[0].index;


        const status = chart.data.labels[index];


        console.log('STATUS CLICK:', status);


        Livewire.dispatch(
            'open-asset-detail-modal',
            {
                status: status,
                company: 'all',
            }
        );

    }
}

JS);
}

}