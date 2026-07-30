<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;

use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;


class AssetCompanyChart extends ChartWidget
{

    protected ?string $heading = 'Asset Berdasarkan Perusahaan';



    public ?string $filter = 'all';



    public array $companyMapping = [];







    protected function getFilters(): ?array
    {
        return [

            'all' => 'Semua Status',

            'Available' => 'Available',

            'In Service' => 'In Service',

            'Not Used' => 'Not Used',

            'Retired' => 'Retired',

        ];
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

                'StatusAsset',

                $this->filter

            );

        }







        $data = $query

            ->select(

                'IDPerusahaan'

            )

            ->selectRaw(

                'COUNT(*) as total'

            )

            ->with('perusahaan')

            ->groupBy(

                'IDPerusahaan'

            )

            ->get();








        $this->companyMapping = $data

            ->mapWithKeys(

                fn ($item) => [

                    $item->perusahaan?->NamaPerusahaan

                    ??

                    'Tanpa Perusahaan'

                    =>

                    $item->IDPerusahaan

                ]

            )

            ->toArray();








        $colors = [

            '#3B82F6',

            '#10B981',

            '#F59E0B',

            '#EF4444',

            '#8B5CF6',

            '#06B6D4',

            '#84CC16',

            '#F97316',

            '#EC4899',

            '#14B8A6',

            '#6366F1',

            '#A855F7',

        ];







        $backgroundColors = [];

        $borderColors = [];








        foreach (

            $data as $index => $item

        ) {

            $color = $colors[$index % count($colors)];

            $backgroundColors[] = $color;

            $borderColors[] = $color;

        }









        return [

            'datasets' => [

                [

                    'label' => 'Jumlah Asset',


                    'data' => $data

                        ->pluck('total')

                        ->toArray(),



                    'backgroundColor' => $backgroundColors,


                    'borderColor' => $borderColors,


                    'borderWidth' => 1,


                    'borderRadius' => 8,

                ],

            ],




            'labels' => $data

                ->map(

                    fn ($item) =>

                    $item->perusahaan?->NamaPerusahaan

                    ??

                    'Tanpa Perusahaan'

                )

                ->toArray(),

        ];

    }









    protected function getType(): string
    {
        return 'bar';
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



        const company =
            chart.data.labels[index];



        const companyId =
            $wire.companyMapping[company];



        console.log(
            'COMPANY CLICK:',
            companyId
        );




        Livewire.dispatch(
            'open-company-detail-modal',
            {
                company: companyId,

                status: $wire.filter,
            }
        );


    }
}

JS);
    }

}