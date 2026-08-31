<?php

namespace App\Filament\Resources\TrxMutasiAssets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;


class TrxMutasiAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([


                Select::make('NoAssetIT')
                    ->label('Asset')
                    ->relationship(
                        name: 'asset',
                        titleAttribute: 'NoAssetIT'
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) =>
                            $record->NoAssetIT . ' - ' . ($record->Nama ?? '-')
                    )
                    ->searchable(['NoAssetIT', 'Nama'])
                    ->preload()
                    ->required(),


                Select::make('NIK')
                    ->label('Karyawan')
                    ->relationship(
                        name: 'karyawan',
                        titleAttribute: 'Nama'
                    )
                    ->searchable(['Nama', 'NIK'])
                    ->preload()
                    ->required(),


                Select::make('IDLokasi')
                    ->label('Lokasi')
                    ->relationship(
                        name: 'lokasi',
                        titleAttribute: 'NamaLokasi'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),


                TextInput::make('NoTransferSAP')
                    ->label('No Transfer SAP'),


                DatePicker::make('TanggalMutasi')
                    ->label('Tanggal Mutasi')
                    ->default(now())
                    ->format('Y-m-d')
                    ->displayFormat('d M Y')
                    ->required(),


                TextInput::make('AksesWebsite')
                    ->label('Akses Website'),


                TextInput::make('AksesEmail')
                    ->label('Akses Email'),


                TextInput::make('Keterangan')
                    ->label('Keterangan')
                    ->columnSpanFull(),

            ]);
    }
}
