<?php

namespace App\Filament\Resources\TrxRetireAssets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;


class TrxRetireAssetForm
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


                TextInput::make('NoRetireSAP')
                    ->label('No Retire SAP'),


                DatePicker::make('TanggalRetire')
                    ->label('Tanggal Retire')
                    ->default(now())
                    ->format('Y-m-d')
                    ->displayFormat('d M Y')
                    ->required(),


                Select::make('AlasanRetire')
                    ->label('Alasan Retire')
                    ->options([
                        'Rusak Total' => 'Rusak Total',
                        'Rusak Partial' => 'Rusak Partial',
                        'Hilang' => 'Hilang',
                        'Others' => 'Others',
                    ])
                    ->required(),


                Select::make('Kondisi')
                    ->label('Kondisi')
                    ->options([
                        'Di jual' => 'Di jual',
                        'Di simpan' => 'Di simpan',
                        'Di hibahkan' => 'Di hibahkan',
                        'Others' => 'Others',
                    ])
                    ->required(),


                Textarea::make('KeteranganDetail')
                    ->label('Keterangan Detail')
                    ->columnSpanFull(),


                TextInput::make('EksekutorIT')
                    ->label('Eksekutor IT'),


                TextInput::make('NilaiSisa')
                    ->label('Nilai Sisa')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

            ]);
    }
}
