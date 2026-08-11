<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use App\Models\MstLokasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class AssetLocationStatusChart extends ChartWidget
{
    protected ?string $heading = 'Status Asset Berdasarkan Lokasi';

    // protected static ?int $sort = 3;

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Semua Lokasi',
        ] + MstLokasi::query()
            ->orderBy('NamaLokasi')
            ->pluck(
                'NamaLokasi',
                'IDLokasi'
            )
            ->toArray();
    }

    protected function getData(): array
    {
        $query = MstAsset::query();

        /*
         * FILTER LOKASI
         */
        if (
            $this->filter !== null &&
            $this->filter !== 'all'
        ) {
            $query->where(
                'IDLokasi',
                $this->filter
            );
        }

        /*
         * STATUS ASSET
         */
        $statuses = [
            'Available',
            'In Service',
            'Not Used',
            'Retired',
        ];

        /*
         * HITUNG ASSET BERDASARKAN STATUS
         */
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

        console.log(
            'LOCATION STATUS CLICK:',
            status
        );

        Livewire.dispatch(
            'open-location-status-detail-modal',
            {
                status: status,
                location: $wire.filter,
            }
        );
    }
}

JS);
    }
}