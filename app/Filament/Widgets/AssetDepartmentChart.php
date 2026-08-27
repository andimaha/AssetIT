<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use App\Models\MstDepartemen;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class AssetDepartmentChart extends ChartWidget
{
    protected ?string $heading = 'Asset Berdasarkan Department';

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [

            'all' => 'Semua Department',

        ] + MstDepartemen::query()
            ->orderBy('NamaDept')
            ->pluck(
                'NamaDept',
                'IDDept'
            )
            ->toArray();
    }

    protected function getData(): array
    {
        /*
         * ==========================================================
         * SEMUA DEPARTMENT
         * ==========================================================
         *
         * Menampilkan jumlah asset berdasarkan Department.
         */
        if (
            $this->filter === null ||
            $this->filter === 'all'
        ) {

            $result = MstAsset::query()
                ->leftJoin(
                    'mstkaryawan',
                    'mstasset.NIK',
                    '=',
                    'mstkaryawan.NIK'
                )
                ->leftJoin(
                    'mstdepartemen',
                    'mstkaryawan.IDDept',
                    '=',
                    'mstdepartemen.IDDept'
                )
                ->select([
                    'mstdepartemen.IDDept as department_id',
                    'mstdepartemen.NamaDept as department_name',
                ])
                ->selectRaw(
                    'COUNT(mstasset.NoAssetIT) as total'
                )
                ->groupBy(
                    'mstdepartemen.IDDept',
                    'mstdepartemen.NamaDept'
                )
                ->orderByDesc('total')
                ->get();

            $labels = $result
                ->map(
                    fn ($row) =>
                        $row->department_name
                            ?: 'Tidak Diketahui'
                )
                ->toArray();

            $data = $result
                ->pluck('total')
                ->map(
                    fn ($total) =>
                        (int) $total
                )
                ->toArray();

            $departmentIds = $result
                ->map(
                    fn ($row) =>
                        $row->department_id !== null
                            ? (int) $row->department_id
                            : null
                )
                ->toArray();

        }

        /*
         * ==========================================================
         * DEPARTMENT TERTENTU
         * ==========================================================
         *
         * Menampilkan jumlah asset berdasarkan Jenis Asset
         * pada Department yang dipilih.
         */
        else {

            $result = MstAsset::query()
                ->join(
                    'mstkaryawan',
                    'mstasset.NIK',
                    '=',
                    'mstkaryawan.NIK'
                )
                ->where(
                    'mstkaryawan.IDDept',
                    $this->filter
                )
                ->selectRaw(
                    'mstasset.Jenis, COUNT(*) as total'
                )
                ->groupBy('mstasset.Jenis')
                ->orderByDesc('total')
                ->get();

            $labels = $result
                ->pluck('Jenis')
                ->map(
                    fn ($jenis) =>
                        $jenis ?: 'Tidak Diketahui'
                )
                ->toArray();

            $data = $result
                ->pluck('total')
                ->map(
                    fn ($total) =>
                        (int) $total
                )
                ->toArray();

            /*
             * Semua data pada chart berasal dari Department
             * yang sedang dipilih.
             */
            $departmentIds = array_fill(
                0,
                count($labels),
                (int) $this->filter
            );
        }


        /*
         * ==========================================================
         * WARNA CHART
         * ==========================================================
         */

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
            '#0EA5E9',
            '#22C55E',
            '#EAB308',
            '#F43F5E',
            '#7C3AED',
            '#DB2777',
            '#0D9488',
            '#EA580C',
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

                    /*
                     * Digunakan ketika chart diklik.
                     */
                    'departmentIds' => $departmentIds,

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

        const label =
            chart.data.labels[index];

        const departmentId =
            chart.data.datasets[0].departmentIds[index];

        console.log(
            'CHART CLICK:',
            label
        );

        console.log(
            'DEPARTMENT ID:',
            departmentId
        );

        /*
         * Jika filter = all
         * maka label chart adalah nama Department.
         *
         * Jika filter Department tertentu
         * maka label chart adalah Jenis Asset.
         */
        Livewire.dispatch(
            'open-asset-department-modal',
            {
                departmentId: departmentId,
                department: $wire.filter === 'all'
                    ? label
                    : $wire.filter,
            }
        );
    }
}

JS);
    }
}
