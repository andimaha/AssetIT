<?php

namespace App\Filament\Exports;

use App\Models\TrxServiceAsset;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;


class TrxServiceAssetExporter extends Exporter
{

    protected static ?string $model = TrxServiceAsset::class;



    public static function getColumns(): array
    {

        return [


            ExportColumn::make('NoAssetIT')
                ->label('No Asset')
                ->state(function (TrxServiceAsset $record) {

                    return $record->asset?->NoAssetIT ?? '-';

                }),



            ExportColumn::make('NoAssetSAP')
                ->label('No Asset SAP')
                ->state(function (TrxServiceAsset $record) {

                    return $record->asset?->NoAssetSAP ?? '-';

                }),



            ExportColumn::make('NamaAsset')
                ->label('Nama Asset')
                ->state(function (TrxServiceAsset $record) {

                    return $record->asset?->Nama ?? '-';

                }),




            ExportColumn::make('Perusahaan')
                ->label('Perusahaan')
                ->state(function (TrxServiceAsset $record) {

                    return $record->asset?->perusahaan?->NamaPerusahaan ?? '-';

                }),





            ExportColumn::make('TanggalMasuk')
                ->label('Tanggal Masuk')
                ->state(function (TrxServiceAsset $record) {

                    return $record->TanggalMasuk
                        ? Carbon::parse($record->TanggalMasuk)
                            ->format('d-m-Y')
                        : '-';

                }),





            ExportColumn::make('TanggalSelesai')
                ->label('Tanggal Selesai')
                ->state(function (TrxServiceAsset $record) {

                    return $record->TanggalSelesai
                        ? Carbon::parse($record->TanggalSelesai)
                            ->format('d-m-Y')
                        : '-';

                }),





            ExportColumn::make('LamaPerbaikan')
                ->label('Lama Perbaikan')
                ->state(function (TrxServiceAsset $record) {


                    if (!$record->TanggalSelesai) {

                        return 'Belum Selesai';

                    }



                    $mulai = Carbon::parse(
                        $record->TanggalMasuk
                    )
                    ->startOfDay();



                    $selesai = Carbon::parse(
                        $record->TanggalSelesai
                    )
                    ->startOfDay();




                    return $mulai
                        ->diffInDays($selesai)
                        . ' Hari';


                }),





            ExportColumn::make('JenisService')
                ->label('Jenis Service'),





            ExportColumn::make('Kerusakan')
                ->label('Kerusakan'),





            ExportColumn::make('Tindakan')
                ->label('Tindakan'),





            ExportColumn::make('Vendor')
                ->label('Vendor Service')
                ->state(function (TrxServiceAsset $record) {

                    return $record->vendor?->NamaVendor ?? '-';

                }),





            ExportColumn::make('Biaya')
                ->label('Biaya'),





            ExportColumn::make('StatusService')
                ->label('Status Service'),





            ExportColumn::make('Oleh')
                ->label('Teknisi IT'),


        ];

    }





    public static function getCompletedNotificationBody(Export $export): string
    {

        $body =
            'Export Service Asset selesai dengan '
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