<?php

namespace App\Filament\Resources\TrxMutasiAssets\Schemas;


use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;



class TrxMutasiAssetForm
{


    public static function configure(Schema $schema): Schema
    {

        return $schema->components([



            Select::make('NoAssetIT')

                ->label('Asset')

                ->relationship(
                    'asset',
                    'NoAssetIT'
                )

                ->searchable()
                ->preload()
                ->required(),




            Select::make('NIK')

                ->label('Karyawan')

                ->relationship(
                    'karyawan',
                    'Nama'
                )

                ->searchable([
                    'Nama',
                    'NIK'
                ])

                ->preload()

                ->required(),




            Select::make('IDLokasi')

                ->label('Lokasi')

                ->relationship(
                    'lokasi',
                    'NamaLokasi'
                )

                ->searchable()
                ->preload(),




            TextInput::make('NoTransferSAP')
                ->label('No Transfer SAP'),




            DatePicker::make('TanggalMutasi')
                ->default(now())
                    ->format('Y-m-d') ->displayFormat('d M Y')
                ->required(),




            TextInput::make('AksesWebsite'),


            TextInput::make('AksesEmail'),





            TextInput::make('Keterangan'),



        ]);

    }


}