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

        $backgroundColors = [];

        foreach ($services as $index => $total) {
            $backgroundColors[] = $colors[$index % count($colors)];
        }

        return [

            'datasets' => [

                [

                    'label' => 'Jumlah Service',

                    'data' => $services
                        ->values()
                        ->toArray(),

                    'backgroundColor' => $backgroundColors,

                    'borderColor' => $backgroundColors,

                    'borderWidth' => 1,

                    'borderRadius' => 8,

                ]

            ],

            'labels' => $services

                ->keys()

                ->map(fn($tahun) => (string) $tahun)

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
        display:false
    }
},

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