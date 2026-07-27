<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use Filament\Widgets\ChartWidget;

class AssetCompanyChart extends ChartWidget
{
    protected ?string $heading = 'Asset Berdasarkan Perusahaan';

    protected function getData(): array
    {
        $data = MstAsset::query()
            ->selectRaw('IDPerusahaan, COUNT(*) as total')
            ->with('perusahaan')
            ->groupBy('IDPerusahaan')
            ->get();

        $colors = [
            '#3B82F6',
            '#10B981',
            '#F59E0B',
            '#EF4444',
            '#8B5CF6',
            '#06B6D4',
            '#84CC16',
            '#F97316',
            '#EC4899',
            '#14B8A6',
            '#6366F1',
            '#A855F7',
        ];

        $backgroundColors = [];
        $borderColors = [];

        foreach ($data as $index => $item) {
            $color = $colors[$index % count($colors)];

            $backgroundColors[] = $color;
            $borderColors[] = $color;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Asset',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $borderColors,
                    'borderWidth' => 1,
                    'borderRadius' => 8,
                ],
            ],

            'labels' => $data
                ->map(fn ($item) => $item->perusahaan?->NamaPerusahaan ?? 'Tanpa Perusahaan')
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}