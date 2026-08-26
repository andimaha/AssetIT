<?php

namespace App\Filament\Exports;

use App\Models\MstAsset;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class MstAssetExporter extends Exporter
{
    protected static ?string $model = MstAsset::class;

    public static function getColumns(): array
    {
        return [

            ExportColumn::make('NoAssetIT')
                ->label('No Asset IT'),

            ExportColumn::make('NoAssetSAP')
                ->label('No Asset SAP'),

            ExportColumn::make('Jenis')
                ->label('Jenis Asset'),

            ExportColumn::make('Nama')
                ->label('Nama Asset'),

            ExportColumn::make('PN')
                ->label('Part Number'),

            ExportColumn::make('SN')
                ->label('Serial Number'),

            ExportColumn::make('PN_LCD')
                ->label('PN LCD'),

            ExportColumn::make('SN_LCD')
                ->label('SN LCD'),

            ExportColumn::make('RAM')
                ->label('RAM'),

            ExportColumn::make('JenisOS')
                ->label('Operating System'),

            ExportColumn::make('ComputerName')
                ->label('Computer Name'),

            ExportColumn::make('IPAddress')
                ->label('IP Address'),

            ExportColumn::make('Lapor')
                ->label('Lapor')
                ->state(function (MstAsset $record) {
                    return $record->Lapor ?? '-';
                }),

            ExportColumn::make('StatusBeli')
                ->label('Status Pembelian'),

            ExportColumn::make('TanggalBeli')
                ->label('Tanggal Beli')
                ->state(function (MstAsset $record) {

                    return $record->TanggalBeli
                        ? Carbon::parse($record->TanggalBeli)
                            ->format('d-m-Y')
                        : '-';

                }),

            ExportColumn::make('Harga')
                ->label('Harga'),

            ExportColumn::make('Vendor')
                ->label('Vendor')
                ->state(function (MstAsset $record) {

                    return $record->vendor?->NamaVendor ?? '-';

                }),

            ExportColumn::make('Garansi')
                ->label('Garansi')
                ->state(function (MstAsset $record) {

                    return ($record->Garansi && $record->Garansi > 0)
                        ? $record->Garansi . ' Tahun'
                        : 'Tidak Ada';

                }),

            ExportColumn::make('DateWarranty')
                ->label('Berakhir Garansi')
                ->state(function (MstAsset $record) {

                    return $record->DateWarranty
                        ? Carbon::parse($record->DateWarranty)
                            ->format('d-m-Y')
                        : '-';

                }),

            ExportColumn::make('Perusahaan')
                ->label('Perusahaan')
                ->state(function (MstAsset $record) {

                    return $record->perusahaan?->NamaPerusahaan ?? '-';

                }),

            ExportColumn::make('PemegangAsset')
                ->label('Pemegang Asset')
                ->state(function (MstAsset $record) {

                    return $record->karyawan?->Nama ?? '-';

                }),

            ExportColumn::make('Departemen')
                ->label('Departemen')
                ->state(function (MstAsset $record) {

                    return $record->karyawan?->Departemen?->NamaDept ?? '-';

                }),

            ExportColumn::make('Lokasi')
                ->label('Lokasi Asset')
                ->state(function (MstAsset $record) {

                    return $record->lokasi?->NamaLokasi ?? '-';

                }),

            ExportColumn::make('StatusAsset')
                ->label('Status Asset'),

            ExportColumn::make('Keterangan')
                ->label('Keterangan')
                ->state(function (MstAsset $record) {

                    return $record->Keterangan ?? '-';

                }),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body =
            'Export Asset selesai dengan '
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
