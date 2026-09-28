<?php

namespace App\Filament\Widgets;

use App\Models\ItRequest;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class ItRequestTypeChart extends ChartWidget
{
    protected ?string $heading = 'Permintaan IT';

    /*
    |--------------------------------------------------------------------------
    | DEFAULT FILTER
    |--------------------------------------------------------------------------
    |
    | Format:
    |
    | YYYY
    |     = semua bulan dalam tahun tersebut
    |
    | YYYY-MM
    |     = bulan tertentu
    |
    */

    public ?string $filter = null;


    /*
    |--------------------------------------------------------------------------
    | FILTER OPTIONS
    |--------------------------------------------------------------------------
    */

    protected function getFilters(): ?array
    {
        $currentYear = now()->year;

        /*
        |--------------------------------------------------------------------------
        | AMBIL TAHUN YANG MEMANG ADA DATA
        |--------------------------------------------------------------------------
        */

        $years = ItRequest::query()
            ->selectRaw('YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->groupByRaw('YEAR(created_at)')
            ->orderByRaw('YEAR(created_at) DESC')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN TAHUN SEKARANG SELALU ADA
        |--------------------------------------------------------------------------
        */

        if (! in_array($currentYear, $years, true)) {
            array_unshift($years, $currentYear);
        }


        /*
        |--------------------------------------------------------------------------
        | SORT DESC
        |--------------------------------------------------------------------------
        */

        $years = collect($years)
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | BUILD FILTER
        |--------------------------------------------------------------------------
        */

        $filters = [];

        foreach ($years as $year) {

            /*
            |--------------------------------------------------------------------------
            | DEFAULT: SEMUA BULAN
            |--------------------------------------------------------------------------
            */

            $filters[(string) $year] =
                $year . ' — Semua Bulan';


            /*
            |--------------------------------------------------------------------------
            | BULAN
            |--------------------------------------------------------------------------
            */

            for ($month = 1; $month <= 12; $month++) {

                $value =
                    $year
                    . '-'
                    . str_pad(
                        $month,
                        2,
                        '0',
                        STR_PAD_LEFT
                    );

                $monthName = Carbon::create(
                    $year,
                    $month,
                    1
                )->translatedFormat('F');

                $filters[$value] =
                    $year
                    . ' — '
                    . $monthName;
            }
        }

        return $filters;
    }


    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    protected function getData(): array
    {
        /*
        |--------------------------------------------------------------------------
        | DEFAULT FILTER
        |--------------------------------------------------------------------------
        */

        $filter =
            $this->filter
            ??
            (string) now()->year;


        /*
        |--------------------------------------------------------------------------
        | PARSE YEAR / MONTH
        |--------------------------------------------------------------------------
        */

        if (str_contains($filter, '-')) {

            [$year, $month] =
                array_map(
                    'intval',
                    explode(
                        '-',
                        $filter
                    )
                );

        } else {

            $year =
                (int) $filter;

            $month = null;
        }


        /*
        |--------------------------------------------------------------------------
        | QUERY
        |--------------------------------------------------------------------------
        */

        $query = ItRequest::query()
            ->whereYear(
                'created_at',
                $year
            );


        /*
        |--------------------------------------------------------------------------
        | FILTER BULAN
        |--------------------------------------------------------------------------
        */

        if ($month !== null) {

            $query->whereMonth(
                'created_at',
                $month
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA
        |--------------------------------------------------------------------------
        */

        $requests = $query
            ->orderBy(
                'created_at',
                'asc'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | LABEL BULAN
        |--------------------------------------------------------------------------
        */

        if ($month !== null) {

            $labels = [
                Carbon::create(
                    $year,
                    $month,
                    1
                )->translatedFormat('F'),
            ];

        } else {

            $labels = [];

            for ($i = 1; $i <= 12; $i++) {

                $labels[] =
                    Carbon::create(
                        $year,
                        $i,
                        1
                    )->translatedFormat('F');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        |
        | Sekarang hanya ada SATU dataset.
        |
        | Tidak ada lagi pembagian berdasarkan jenisPermintaan.
        |
        */

        $data = [];


        /*
        |--------------------------------------------------------------------------
        | BULAN TERPILIH
        |--------------------------------------------------------------------------
        */

        if ($month !== null) {

            $data[] =
                $requests->count();

        }


        /*
        |--------------------------------------------------------------------------
        | SEMUA BULAN
        |--------------------------------------------------------------------------
        */

        else {

            for (
                $currentMonth = 1;
                $currentMonth <= 12;
                $currentMonth++
            ) {

                $total = $requests
                    ->filter(
                        function ($request) use (
                            $currentMonth
                        ) {

                            return
                                Carbon::parse(
                                    $request->created_at
                                )->month
                                ===
                                $currentMonth;
                        }
                    )
                    ->count();

                $data[] = $total;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return [

            'datasets' => [

                [

                    'label' =>
                        'Total Request',

                    'data' =>
                        $data,

                    'backgroundColor' =>
                        '#8B5CF6',

                    'borderColor' =>
                        '#7C3AED',

                    'borderWidth' =>
                        1,

                    'borderRadius' =>
                        6,

                    'borderSkipped' =>
                        false,

                ],

            ],

            'labels' =>
                $labels,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | CHART TYPE
    |--------------------------------------------------------------------------
    */

    protected function getType(): string
    {
        return 'bar';
    }


    /*
    |--------------------------------------------------------------------------
    | CHART OPTIONS
    |--------------------------------------------------------------------------
    */

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'

{
    responsive: true,

    maintainAspectRatio: false,

    interaction: {
        mode: 'nearest',
        intersect: true
    },

    plugins: {

        legend: {
            display: false
        },

        tooltip: {

            callbacks: {

                label: function(context)
                {

                    const value =
                        context.parsed.y
                        || 0;

                    return value + ' Request';

                }

            }

        }

    },

    scales: {

        x: {

            stacked: false,

            title: {
                display: true,
                text: 'Bulan'
            },

            grid: {
                display: false
            }

        },

        y: {

            stacked: false,

            beginAtZero: true,

            ticks: {

                precision: 0

            },

            title: {
                display: true,
                text: 'Jumlah Request'
            }

        }

    },

    onClick(event, elements, chart)
    {

        if (!elements.length) {
            return;
        }


        const element =
            elements[0];


        const index =
            element.index;


        const bulan =
            chart
                .data
                .labels[index];


        const filter =
            $wire.filter;


        console.log(
            'IT REQUEST CLICK:',
            {
                bulan: bulan,
                filter: filter
            }
        );


        Livewire.dispatch(
            'open-it-request-detail-modal',
            {
                bulan: bulan,
                filter: filter
            }
        );

    }

}

JS);
    }
}
