<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use Filament\Widgets\ChartWidget;

class AssetStatusChart extends ChartWidget
{
    protected ?string $heading = 'Asset Berdasarkan Status';

    protected function getData(): array
    {
        $data = MstAsset::selectRaw(
            'StatusAsset, COUNT(*) as total'
        )
            ->groupBy('StatusAsset')
            ->pluck('total', 'StatusAsset');

        $colors = [
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Amber
            '#EF4444', // Red
            '#8B5CF6', // Purple
            '#06B6D4', // Cyan
            '#84CC16', // Lime
            '#F97316', // Orange
            '#EC4899', // Pink
            '#14B8A6', // Teal
            '#6366F1', // Indigo
            '#A855F7', // Violet
        ];

        $backgroundColors = [];
        $borderColors = [];

        foreach (array_values($data->toArray()) as $index => $value) {
            $color = $colors[$index % count($colors)];

            $backgroundColors[] = $color;
            $borderColors[] = '#FFFFFF';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Asset',
                    'data' => $data->values()->toArray(),

                    // Warna tiap slice
                    'backgroundColor' => $backgroundColors,

                    // Border putih agar antar slice terlihat jelas
                    'borderColor' => $borderColors,
                    'borderWidth' => 2,

                    // Efek saat hover
                    'hoverOffset' => 12,
                ],
            ],

            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}