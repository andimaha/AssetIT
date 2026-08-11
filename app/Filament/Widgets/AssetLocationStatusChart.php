<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use App\Models\MstLokasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class AssetLocationStatusChart extends ChartWidget
{
    protected ?string $heading = 'Asset Berdasarkan Lokasi';

    // protected static ?int $sort = 3;

    /**
     * Filter STATUS
     */
    public ?string $filter = 'all';

    /**
     * Daftar filter status
     */
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

    /**
     * Data Chart
     *
     * Label  : Lokasi
     * Data   : Jumlah Asset
     * Filter : Status
     */
    protected function getData(): array
    {
        /**
         * Ambil semua lokasi
         */
        $locations = MstLokasi::query()
            ->orderBy('NamaLokasi')
            ->get([
                'IDLokasi',
                'NamaLokasi',
            ]);

        /**
         * Query Asset
         */
        $query = MstAsset::query();

        /**
         * FILTER STATUS
         */
        if (
            $this->filter !== null &&
            $this->filter !== 'all'
        ) {
            $query->where(
                'StatusAsset',
                $this->filter
            );
        }

        /**
         * Hitung asset berdasarkan lokasi
         */
        $result = $query
            ->selectRaw(
                'IDLokasi, COUNT(*) as total'
            )
            ->groupBy('IDLokasi')
            ->pluck(
                'total',
                'IDLokasi'
            );

        /**
         * ID Lokasi
         */
        $locationIds = $locations
            ->pluck('IDLokasi')
            ->toArray();

        /**
         * Nama Lokasi
         */
        $labels = $locations
            ->map(
                fn ($location) =>
                    $location->NamaLokasi ?? '-'
            )
            ->toArray();

        /**
         * Jumlah Asset
         */
        $data = $locations
            ->map(
                fn ($location) =>
                    (int) (
                        $result[$location->IDLokasi] ?? 0
                    )
            )
            ->toArray();

        /**
         * Warna Bar
         */
        $colors = [
            '#3B82F6',
            '#10B981',
            '#F59E0B',
            '#EF4444',
            '#8B5CF6',
            '#06B6D4',
            '#EC4899',
            '#84CC16',
            '#F97316',
            '#6366F1',
            '#14B8A6',
            '#EAB308',
            '#A855F7',
            '#0EA5E9',
            '#22C55E',
        ];

        $backgroundColors = collect($data)
            ->map(
                fn ($value, $index) =>
                    $colors[
                        $index % count($colors)
                    ]
            )
            ->toArray();

        return [
            /**
             * Custom property untuk JavaScript.
             */
            'locationIds' => $locationIds,

            'datasets' => [
                [
                    'label' => $this->filter === 'all'
                        ? 'Jumlah Asset'
                        : 'Jumlah Asset - ' . $this->filter,

                    'data' => $data,

                    'backgroundColor' => $backgroundColors,

                    'borderColor' => '#FFFFFF',

                    'borderWidth' => 2,

                    'borderRadius' => 6,

                    'hoverOffset' => 8,
                ],
            ],

            'labels' => $labels,
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
    responsive: true,

    maintainAspectRatio: false,

    plugins: {
        legend: {
            display: true,
        },

        tooltip: {
            callbacks: {
                label: function(context) {
                    return 'Jumlah Asset: ' + context.parsed.y;
                }
            }
        }
    },

    scales: {
        x: {
            title: {
                display: true,
                text: 'Lokasi'
            },

            ticks: {
                autoSkip: false,
                maxRotation: 45,
                minRotation: 0
            }
        },

        y: {
            beginAtZero: true,

            title: {
                display: true,
                text: 'Jumlah Asset'
            },

            ticks: {
                precision: 0
            }
        }
    },

    onClick(event, elements, chart)
    {
        if (!elements.length) {
            return;
        }

        const index = elements[0].index;

        const locationId =
            chart.data.locationIds[index];

        const locationName =
            chart.data.labels[index];

        if (!locationId) {
            return;
        }

        console.log(
            'LOCATION CLICK:',
            locationId,
            locationName
        );

        Livewire.dispatch(
            'open-location-status-detail-modal',
            {
                location: locationId,
                status: $wire.filter,
            }
        );
    }
}

JS);
    }
}