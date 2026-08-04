<?php

namespace App\Filament\Resources\TrxServiceAssets\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

use Filament\Tables\Table;

use Carbon\Carbon;

class TrxServiceAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([

                TextColumn::make('asset.NoAssetIT')
                    ->label('Asset')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('TanggalMasuk')
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->sortable(),


                TextColumn::make('TanggalSelesai')
                    ->label('Tanggal Selesai')
                    ->date('d M Y')
                    ->sortable(),


                TextColumn::make('lama_perbaikan')
                    ->label('Lama Perbaikan')
                    ->state(function ($record) {

                        if (!$record->TanggalSelesai) {
                            return 'Belum Selesai';
                        }

                        $mulai = Carbon::parse($record->TanggalMasuk);
                        $selesai = Carbon::parse($record->TanggalSelesai);

                        return $mulai->diffInDays($selesai) . ' Hari';

                    })
                    ->badge(),


                TextColumn::make('JenisService')
                    ->label('Jenis Service')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('Kerusakan')
                    ->limit(40),


                TextColumn::make('Tindakan')
                    ->limit(40),


                TextColumn::make('vendor.NamaVendor')
                    ->label('Vendor Service')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('Biaya')
                    ->label('Biaya')
                    ->money('IDR', locale: 'id')
                    ->sortable(),


                TextColumn::make('StatusService')
                    ->label('Status')
                    ->badge()
                    ->sortable(),


                TextColumn::make('Oleh')
                    ->label('Teknisi IT')
                    ->searchable(),

            ])


            ->filters([


                SelectFilter::make('tahun')
                    ->label('Tahun Service')
                    ->options(function () {

                        return \App\Models\TrxServiceAsset::query()
                            ->selectRaw('YEAR(TanggalMasuk) as tahun')
                            ->distinct()
                            ->orderByDesc('tahun')
                            ->pluck('tahun', 'tahun');

                    })
                    ->query(function ($query, array $data) {

                        if (!empty($data['value'])) {

                            $query->whereYear(
                                'TanggalMasuk',
                                $data['value']
                            );

                        }

                    }),



                SelectFilter::make('StatusService')
                    ->label('Status Service')
                    ->options([

                        'Proses' => 'Proses',

                        'Selesai' => 'Selesai',

                        'Unrepairable' => 'Unrepairable',

                    ]),


                SelectFilter::make('JenisService')
                    ->label('Jenis Service')
                    ->options([

                        'Maintenance' => 'Maintenance',

                        'Perbaikan' => 'Perbaikan',

                        'Upgrade' => 'Upgrade',

                    ]),


            ])


            ->actions([

                EditAction::make(),

                DeleteAction::make(),

            ]);
    }
}