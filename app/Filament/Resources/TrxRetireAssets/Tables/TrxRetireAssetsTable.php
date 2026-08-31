<?php

namespace App\Filament\Resources\TrxRetireAssets\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class TrxRetireAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort(
                'TanggalRetire',
                'desc'
            )

            ->columns([


                TextColumn::make('asset.NoAssetIT')
                    ->label('ASSET')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('asset.Nama')
                    ->label('NAMA ASSET')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('NoRetireSAP')
                    ->label('NO RETIRE SAP')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('TanggalRetire')
                    ->label('TANGGAL RETIRE')
                    ->date('d M Y')
                    ->sortable(),


                TextColumn::make('AlasanRetire')
                    ->label('ALASAN RETIRE')
                    ->badge()
                    ->searchable()
                    ->sortable(),


                TextColumn::make('Kondisi')
                    ->label('KONDISI')
                    ->badge()
                    ->searchable()
                    ->sortable(),


                TextColumn::make('EksekutorIT')
                    ->label('EKSEKUTOR IT')
                    ->searchable()
                    ->toggleable(),


                TextColumn::make('NilaiSisa')
                    ->label('NILAI SISA')
                    ->money('IDR')
                    ->sortable(),


                TextColumn::make('KeteranganDetail')
                    ->label('KETERANGAN')
                    ->limit(50)
                    ->tooltip(
                        fn ($record) => $record->KeteranganDetail
                    )
                    ->wrap()
                    ->searchable()
                    ->toggleable(),

            ])

            ->recordActions([

                EditAction::make(),

                DeleteAction::make(),

            ]);
    }
}
