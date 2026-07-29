<?php

namespace App\Filament\Resources\TrxMutasiAssets\Tables;


use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;



class TrxMutasiAssetsTable
{


    public static function configure(Table $table): Table
    {


        return $table

            ->columns([


                TextColumn::make('asset.NoAssetIT')
                    ->label('Asset')
                    ->searchable(),



                TextColumn::make('karyawan.NIK')
                    ->label('NIK'),



                TextColumn::make('karyawan.Nama')
                    ->label('User'),



                TextColumn::make('karyawan.departemen.NamaDept')
                    ->label('Departemen'),



                TextColumn::make('lokasi.NamaLokasi')
                    ->label('Lokasi'),



                TextColumn::make('TanggalMutasi')
                    ->date('d M Y'),



            ])


            ->actions([

                EditAction::make(),

                DeleteAction::make(),

            ]);


    }


}