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
        | Ambil data berdasarkan Tahun + Jenis Service
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
        | Daftar Tahun
        |--------------------------------------------------------------------------
        */

        $years = $services

            ->pluck('tahun')

            ->unique()

            ->sort()

            ->values();


        /*
        |--------------------------------------------------------------------------
        | Daftar Jenis Service
        |--------------------------------------------------------------------------
        */

        $jenisServices = $services

            ->pluck('jenis')

            ->unique()

            ->sort()

            ->values();


        /*
        |--------------------------------------------------------------------------
        | Warna Setiap Jenis Service
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
        | Buat Dataset Stack Berdasarkan Jenis
        |--------------------------------------------------------------------------
        */

        $datasets = [];

        foreach ($jenisServices as $index => $jenis) {

            $data = [];

            foreach ($years as $tahun) {

                $row = $services

                    ->first(
                        fn ($item) =>
                            (string) $item->tahun === (string) $tahun
                            &&
                            (string) $item->jenis === (string) $jenis
                    );

                $data[] = $row
                    ? (int) $row->total
                    : 0;
            }


            $color = $colors[
                $index % count($colors)
            ];


            $datasets[] = [

                'label' => $jenis,

                'data' => $data,

                'backgroundColor' => $color,

                'borderColor' => $color,

                'borderWidth' => 1,

                'borderRadius' => 4,

                'stack' => 'service',

            ];
        }


        return [

            'datasets' => $datasets,

            'labels' => $years

                ->map(
                    fn ($tahun) =>
                        (string) $tahun
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

responsive:true,

plugins:{

    legend:{
        display:true,
        position:'bottom'
    }

},

scales:{

    x:{

        stacked:true

    },

    y:{

        stacked:true,

        beginAtZero:true,

        ticks:{

            precision:0

        }

    }

},

onClick(event,elements,chart)
{

    if(!elements.length)
    {
        return;
    }


    let element = elements[0];

    let index = element.index;

    let datasetIndex = element.datasetIndex;


    let tahun = chart.data.labels[index];

    let jenis = chart.data.datasets[datasetIndex].label;


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
