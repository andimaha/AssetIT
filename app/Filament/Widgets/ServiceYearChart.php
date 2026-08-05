<?php

namespace App\Filament\Widgets;

use App\Models\TrxServiceAsset;
use App\Models\MstPerusahaan;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;


class ServiceYearChart extends ChartWidget
{

    protected ?string $heading = 'Service Asset Berdasarkan Tahun';


    public ?string $filter = 'all';



    protected function getFilters(): ?array
    {

        return [

            'all' => 'Semua Perusahaan',

        ]

        +

        MstPerusahaan::query()

            ->orderBy('NamaPerusahaan')

            ->pluck(
                'NamaPerusahaan',
                'IDPerusahaan'
            )

            ->toArray();

    }





    protected function getData(): array
    {


        $query = TrxServiceAsset::query()
            ->with('asset');



        if(
            $this->filter !== 'all'
            &&
            $this->filter !== null
        ){

            $query->whereHas(
                'asset',
                function($q){

                    $q->where(
                        'IDPerusahaan',
                        $this->filter
                    );

                }
            );

        }



        $services = $query

            ->selectRaw(
                'YEAR(TanggalMasuk) as tahun,
                COUNT(*) as total'
            )

            ->groupBy('tahun')

            ->orderBy('tahun')

            ->pluck(
                'total',
                'tahun'
            );




        return [


            'datasets'=>[

                [

                    'label'=>'Jumlah Service',


                    'data'=>$services
                        ->values()
                        ->toArray(),



                    'backgroundColor'=>'#F97316',


                    'borderRadius'=>8,

                ]

            ],



            'labels'=>$services

                ->keys()

                ->map(fn($tahun)=> (string)$tahun)

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

onClick(event,elements,chart)

{

    if(!elements.length)
    {
        return;
    }


    let index = elements[0].index;


    let tahun = chart.data.labels[index];



    Livewire.dispatch(
        'open-service-year-modal',
        {
            tahun:tahun,
            company:$wire.filter
        }
    );


}

}

JS);

    }


}