<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

use Carbon\Carbon;


class WarrantyExpiringAssets extends BaseWidget
{

    protected static ?string $heading = 'Warranty Akan Habis';



    public function table(Table $table): Table
    {
        return $table

            ->query(

                MstAsset::query()

                    ->whereNotNull('DateWarranty')

                    ->whereDate(
                        'DateWarranty',
                        '>=',
                        now()
                    )

                    ->whereDate(
                        'DateWarranty',
                        '<=',
                        now()->addMonth()
                    )

                    ->orderBy(
                        'DateWarranty',
                        'asc'
                    )

            )


            ->columns([



                Tables\Columns\TextColumn::make('NoAssetIT')
                    ->label('NO ASSET')
                    ->searchable()
                    ->sortable(),




                Tables\Columns\TextColumn::make('Nama')
                    ->label('NAMA ASSET')
                    ->searchable()
                    ->sortable(),




                Tables\Columns\TextColumn::make('perusahaan.NamaPerusahaan')
                    ->label('PERUSAHAAN')
                    ->searchable(),




                Tables\Columns\TextColumn::make('karyawan.Nama')
                    ->label('PEMEGANG')
                    ->placeholder('-')
                    ->searchable(),




                Tables\Columns\TextColumn::make('lokasi.NamaLokasi')
                    ->label('LOKASI')
                    ->placeholder('-')
                    ->searchable(),




                Tables\Columns\TextColumn::make('DateWarranty')
                    ->label('BERAKHIR WARRANTY')
                    ->date('d M Y')
                    ->sortable(),





                Tables\Columns\TextColumn::make('DateWarranty')

                    ->label('SISA HARI')

                    ->badge()


                    ->state(function ($record) {


                        $hari = now()

                            ->floatDiffInDays(
                                Carbon::parse(
                                    $record->DateWarranty
                                ),
                                false
                            );


                        return (int) ceil($hari);


                    })


                    ->formatStateUsing(function ($state) {


                        return $state . ' Hari Lagi';


                    })


                    ->color(function ($state) {


                        return match (true) {


                            $state <= 7 =>
                                'danger',


                            $state <= 14 =>
                                'warning',


                            default =>
                                'success',


                        };


                    }),


            ]);

    }

}