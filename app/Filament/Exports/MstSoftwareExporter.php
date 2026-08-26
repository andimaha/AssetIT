<?php

namespace App\Filament\Exports;

use App\Models\MstSoftware;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class MstSoftwareExporter extends Exporter
{
    protected static ?string $model = MstSoftware::class;

    public static function getColumns(): array
    {
        return [

            ExportColumn::make('NamaSoftware')
                ->label('Nama Software'),

            ExportColumn::make('ProductKey')
                ->label('Product Key')
                ->state(function (MstSoftware $record) {

                    return $record->license()->count() . ' Key';

                }),

            ExportColumn::make('Perusahaan')
                ->label('Perusahaan')
                ->state(function (MstSoftware $record) {

                    return $record->license
                        ->pluck('perusahaan.NamaPerusahaan')
                        ->filter()
                        ->unique()
                        ->values()
                        ->implode(', ') ?: '-';

                }),

            ExportColumn::make('SoftCategory')
                ->label('Kategori'),

            ExportColumn::make('Jenis')
                ->label('Jenis'),

            ExportColumn::make('Version')
                ->label('Version'),

            ExportColumn::make('Is32Bit')
                ->label('32 Bit')
                ->formatStateUsing(function ($state) {

                    return $state ? 'Ya' : 'Tidak';

                }),

            ExportColumn::make('Is64Bit')
                ->label('64 Bit')
                ->formatStateUsing(function ($state) {

                    return $state ? 'Ya' : 'Tidak';

                }),

        ];
    }

    public static function getCompletedNotificationBody(
        Export $export
    ): string {

        $body =
            'Export Software selesai dengan '
            .
            Number::format(
                $export->successful_rows
            )
            .
            ' '
            .
            str('row')
                ->plural(
                    $export->successful_rows
                )
            .
            ' berhasil diexport.';

        if (
            $failedRowsCount =
            $export->getFailedRowsCount()
        ) {

            $body .=
                ' '
                .
                Number::format(
                    $failedRowsCount
                )
                .
                ' '
                .
                str('row')
                    ->plural(
                        $failedRowsCount
                    )
                .
                ' gagal diexport.';

        }

        return $body;
    }
}
