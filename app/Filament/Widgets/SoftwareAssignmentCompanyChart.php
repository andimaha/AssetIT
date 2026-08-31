<?php

namespace App\Filament\Widgets;

use App\Models\MstPerusahaan;
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class SoftwareAssignmentCompanyChart extends ChartWidget
{
    protected ?string $heading = 'Software Assignment berdasarkan Pengguna';

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
        $query = DB::table('trxsoftwareassignment as tsa')
            ->join(
                'mstsoftwarelicense as msl',
                'tsa.IDLicense',
                '=',
                'msl.IDLicense'
            )
            ->join(
                'mstsoftware as ms',
                'msl.IDSoftware',
                '=',
                'ms.IDSoftware'
            )
            ->join(
                'mstasset as ma',
                'tsa.NoAssetIT',
                '=',
                'ma.NoAssetIT'
            )
            ->whereNull('tsa.TanggalRevoke')
            ->where('tsa.StatusAssignment', 'Installed');

        if (
            $this->filter !== null &&
            $this->filter !== 'all'
        ) {
            $query->where(
                'ma.IDPerusahaan',
                $this->filter
            );
        }

        $result = $query
            ->select(
                'ms.IDSoftware',
                'ms.NamaSoftware',
            )
            ->selectRaw('COUNT(*) as total')
            ->groupBy(
                'ms.IDSoftware',
                'ms.NamaSoftware'
            )
            ->orderByDesc('total')
            ->get();

        $labels = $result
            ->pluck('NamaSoftware')
            ->map(
                fn ($software) =>
                    $software ?: 'Tidak Diketahui'
            )
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
                    'label' => 'Jumlah Pemakai',
                    'data' => $data,
                    'backgroundColor' => array_slice(
                        $colors,
                        0,
                        count($labels)
                    ),
                    'borderColor' => '#FFFFFF',
                    'borderWidth' => 2,
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
    responsive: true,

    maintainAspectRatio: false,

    plugins: {

        legend: {
            display: true,

            position: 'right',

            labels: {
                usePointStyle: true,

                padding: 15,

                font: {
                    size: 12
                }
            }
        },

        tooltip: {
            enabled: true,

            callbacks: {

                label: function(context) {

                    const label =
                        context.label || '';

                    const value =
                        Number(context.raw || 0);

                    const data =
                        context.dataset.data || [];

                    const total =
                        data.reduce(
                            (sum, item) =>
                                sum + Number(item || 0),
                            0
                        );

                    const percentage =
                        total > 0
                            ? ((value / total) * 100).toFixed(1)
                            : '0.0';

                    return (
                        label +
                        ': ' +
                        value +
                        ' Pemakai (' +
                        percentage +
                        '%)'
                    );
                }

            }
        }
    },

    onClick(event, elements, chart)
    {
        if (!elements.length) {
            return;
        }

        const index =
            elements[0].index;

        const software =
            chart.data.labels[index];

        console.log(
            'SOFTWARE CLICK:',
            software
        );

        Livewire.dispatch(
            'open-software-assignment-modal',
            {
                software: software,
                company: $wire.filter,
            }
        );
    }
}

JS);
}

}
