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

        if (
            $this->filter !== 'all'
            &&
            $this->filter !== null
        ) {

            $query->whereHas(
                'asset',
                function ($q) {

                    $q->where(
                        'IDPerusahaan',
                        $this->filter
                    );

                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DATA TAHUN + JENIS SERVICE
        |--------------------------------------------------------------------------
        */

        $services = $query

            ->selectRaw(
                'YEAR(TanggalMasuk) as tahun,
                COALESCE(JenisService, "Tidak Ada Jenis") as jenis,
                COUNT(*) as total'
            )

            ->groupBy(
                'tahun',
                'jenis'
            )

            ->orderBy('tahun')

            ->get();


        /*
        |--------------------------------------------------------------------------
        | DAFTAR TAHUN
        |--------------------------------------------------------------------------
        */

        $years = $services

            ->pluck('tahun')

            ->unique()

            ->sort()

            ->values();


        /*
        |--------------------------------------------------------------------------
        | DAFTAR JENIS SERVICE
        |--------------------------------------------------------------------------
        */

        $jenisServices = $services

            ->pluck('jenis')

            ->unique()

            ->sort()

            ->values();


        /*
        |--------------------------------------------------------------------------
        | WARNA
        |--------------------------------------------------------------------------
        */

        $colors = [

            '#3B82F6', // Biru
            '#10B981', // Hijau
            '#F59E0B', // Kuning
            '#EF4444', // Merah
            '#8B5CF6', // Ungu
            '#EC4899', // Pink
            '#06B6D4', // Cyan
            '#84CC16', // Lime
            '#F97316', // Orange
            '#6366F1', // Indigo
            '#14B8A6', // Teal
            '#A855F7', // Violet

        ];


        /*
        |--------------------------------------------------------------------------
        | DATASET STACK
        |--------------------------------------------------------------------------
        */

        $datasets = [];

        foreach (
            $jenisServices
            as $index => $jenis
        ) {

            $data = [];

            foreach (
                $years
                as $tahun
            ) {

                $row = $services

                    ->first(
                        fn ($item) =>
                            (string) $item->tahun
                                ===
                            (string) $tahun
                            &&
                            (string) $item->jenis
                                ===
                            (string) $jenis
                    );

                $data[] =
                    $row
                    ? (int) $row->total
                    : 0;
            }


            $color =
                $colors[
                    $index
                    %
                    count($colors)
                ];


            $datasets[] = [

                'label' =>
                    $jenis,

                'data' =>
                    $data,

                'backgroundColor' =>
                    $color,

                'borderColor' =>
                    $color,

                'borderWidth' =>
                    1,

                'borderRadius' =>
                    4,

                'stack' =>
                    'service',

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL PER TAHUN
        |--------------------------------------------------------------------------
        |
        | Total ini BUKAN dataset.
        |
        | Jadi tidak akan membuat garis / warna / legend tambahan.
        |
        */

        $totalPerYear = [];

        foreach (
            $years
            as $tahun
        ) {

            $totalPerYear[] =
                (int) $services

                    ->where(
                        'tahun',
                        $tahun
                    )

                    ->sum(
                        'total'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN DATA
        |--------------------------------------------------------------------------
        */

        return [

            'datasets' =>
                $datasets,

            'labels' =>
                $years

                    ->map(
                        fn ($tahun) =>
                            (string) $tahun
                    )

                    ->toArray(),

            /*
            |--------------------------------------------------------------------------
            | TOTAL DIKIRIM KE JAVASCRIPT
            |--------------------------------------------------------------------------
            */

            'totals' =>
                $totalPerYear,

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

responsive:true,

maintainAspectRatio:false,

layout:{

    padding:{

        top:30

    }

},

plugins:{

    legend:{

        display:true,

        position:'bottom'

    },

    tooltip:{

        callbacks:{

            label:function(context)
            {

                let label =
                    context.dataset.label || '';

                let value =
                    context.parsed.y ?? 0;

                return (
                    label
                    +
                    ': '
                    +
                    value
                );

            },

            footer:function(tooltipItems)
            {

                if(
                    !tooltipItems.length
                ) {
                    return '';
                }

                let chart =
                    tooltipItems[0].chart;

                let index =
                    tooltipItems[0].dataIndex;

                let total =
                    chart.data.totals[index] ?? 0;

                return (
                    'Total Tahun: '
                    +
                    total
                );

            }

        },

        footerFont:{

            weight:'bold'

        }

    }

},

scales:{

    x:{

        stacked:true

    },

    y:{

        stacked:true,

        beginAtZero:true,

        grace:'10%',

        ticks:{

            precision:0

        }

    }

},

/*
|--------------------------------------------------------------------------
| CUSTOM DRAW TOTAL LABEL
|--------------------------------------------------------------------------
|
| Ini bukan line chart.
|
| Kita mengambil posisi bagian PALING ATAS dari stacked bar
| kemudian menggambar angka total tepat di atasnya.
|
*/

animation:{

    onComplete:function(animation)
    {

        const chart =
            animation.chart;

        const ctx =
            chart.ctx;

        const totals =
            chart.data.totals || [];

        const labels =
            chart.data.labels || [];


        ctx.save();


        /*
        |--------------------------------------------------------------------------
        | FONT TOTAL
        |--------------------------------------------------------------------------
        */

        ctx.font =
            'bold 14px Arial';

        ctx.fillStyle =
            '#111827';

        ctx.textAlign =
            'center';

        ctx.textBaseline =
            'bottom';


        /*
        |--------------------------------------------------------------------------
        | LOOP SETIAP TAHUN
        |--------------------------------------------------------------------------
        */

        labels.forEach(
            function(label, index)
            {

                let total =
                    totals[index] ?? 0;


                /*
                |--------------------------------------------------------------------------
                | Jangan tampilkan jika total 0
                |--------------------------------------------------------------------------
                */

                if(total <= 0)
                {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Posisi X BAR
                |--------------------------------------------------------------------------
                |
                | Ambil posisi dari dataset pertama.
                |
                */

                let firstMeta =
                    chart.getDatasetMeta(0);


                if(
                    !firstMeta
                    ||
                    !firstMeta.data[index]
                ) {
                    return;
                }


                let x =
                    firstMeta
                        .data[index]
                        .x;


                /*
                |--------------------------------------------------------------------------
                | Cari posisi TOP dari seluruh STACK
                |--------------------------------------------------------------------------
                */

                let topY =
                    Infinity;


                chart.data.datasets.forEach(
                    function(dataset, datasetIndex)
                    {

                        let meta =
                            chart.getDatasetMeta(
                                datasetIndex
                            );


                        if(
                            !meta
                            ||
                            !meta.data[index]
                        ) {
                            return;
                        }


                        let bar =
                            meta.data[index];


                        /*
                        |--------------------------------------------------------------------------
                        | Hanya dataset yang mempunyai nilai
                        |--------------------------------------------------------------------------
                        */

                        let value =
                            Number(
                                dataset.data[index]
                            ) || 0;


                        if(
                            value <= 0
                        ) {
                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Ambil posisi paling atas
                        |--------------------------------------------------------------------------
                        */

                        if(
                            bar.y < topY
                        ) {
                            topY =
                                bar.y;
                        }

                    }
                );


                if(
                    topY === Infinity
                ) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | JARAK LABEL DARI BAR
                |--------------------------------------------------------------------------
                */

                let y =
                    topY - 7;


                /*
                |--------------------------------------------------------------------------
                | GAMBAR TOTAL
                |--------------------------------------------------------------------------
                */

                ctx.fillText(
                    total.toString(),
                    x,
                    y
                );

            }
        );


        ctx.restore();

    }

},

onClick(event,elements,chart)
{

    if(
        !elements.length
    ) {
        return;
    }


    let element =
        elements[0];


    let index =
        element.index;


    let datasetIndex =
        element.datasetIndex;


    let tahun =
        chart.data.labels[index];


    let jenis =
        chart.data.datasets[
            datasetIndex
        ].label;


    Livewire.dispatch(
        'open-service-year-modal',
        {

            tahun:tahun,

            company:$wire.filter,

            jenis:jenis

        }
    );

}

}

JS);
    }
}
