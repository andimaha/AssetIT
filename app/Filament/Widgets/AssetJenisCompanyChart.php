<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use App\Models\MstPerusahaan;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class AssetJenisCompanyChart extends ChartWidget
{
    protected ?string $heading = 'Jenis Asset terhadap Perusahaan';

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
            $this->filter !== null &&
            $this->filter !== 'all'
        ) {
            $query->where(
                'IDPerusahaan',
                $this->filter
            );
        }

        $result = $query
            ->selectRaw('Jenis, COUNT(*) as total')
            ->groupBy('Jenis')
            ->orderByDesc('total')
            ->get();

        $labels = $result
            ->pluck('Jenis')
            ->map(fn ($jenis) => $jenis ?: 'Tidak Diketahui')
            ->toArray();

        $data = $result
            ->pluck('total')
            ->toArray();

        $colors = [
            '#3B82F6',
            '#10B981',
            '#F59E0B',
            '#EF4444',
            '#8B5CF6',
            '#EC4899',
            '#14B8A6',
            '#F97316',
            '#84CC16',
            '#6366F1',
            '#06B6D4',
            '#A855F7',
        ];

        while (count($colors) < count($labels)) {
            $colors[] = sprintf(
                '#%06X',
                mt_rand(0, 0xFFFFFF)
            );
        }

        return [

            'datasets' => [

                [

                    'label' => 'Jumlah Asset',

                    'data' => $data,

                    'backgroundColor' => array_slice(
                        $colors,
                        0,
                        count($labels)
                    ),

                    'borderColor' => '#FFFFFF',

                    'borderWidth' => 2,

                    'hoverOffset' => 12,

                ],

            ],

            'labels' => $labels,

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

        const jenis = chart.data.labels[index];

        console.log('JENIS CLICK:', jenis);

        Livewire.dispatch(
            'open-asset-jenis-modal',
            {
                jenis: jenis,
                company: $wire.filter,
            }
        );
    }
}

JS);
    }
}