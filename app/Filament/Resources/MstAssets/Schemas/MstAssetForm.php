<?php

namespace App\Filament\Resources\MstAssets\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

use Filament\Schemas\Components\Section;

use Carbon\Carbon;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;


class MstAssetForm
{

    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([



                Section::make('Informasi Asset')

                    ->columns(2)

                    ->schema([



                        TextInput::make('NoAssetIT')

                            ->label('No Asset IT')

                            ->required()

                            ->unique(ignoreRecord: true),




                        TextInput::make('NoAssetSAP')

                            ->label('No Asset SAP'),




                        TextInput::make('Jenis')

                            ->label('Jenis Asset')

                            ->required(),




                        TextInput::make('Nama')

                            ->label('Nama Asset')

                            ->required(),




                        TextInput::make('PN')

                            ->label('Part Number'),




                        TextInput::make('SN')

                            ->label('Serial Number'),




                        TextInput::make('PN_LCD')

                            ->label('Part Number LCD'),




                        TextInput::make('SN_LCD')

                            ->label('Serial Number LCD'),




                        TextInput::make('RAM')

                            ->label('RAM'),




                        TextInput::make('JenisOS')

                            ->label('Operating System'),




                        TextInput::make('ComputerName')

                            ->label('Computer Name'),




                        TextInput::make('IPAddress')

                            ->label('IP Address')

                            ->ip(),




                        TextInput::make('Lapor')

                            ->label('Lapor'),
                        
                        TextInput::make('Keterangan')

                            ->label('Keterangan'),



                    ]),






                Section::make('Pembelian')

                    ->columns(2)

                    ->schema([



                        Select::make('StatusBeli')

                            ->label('Status Pembelian')

                            ->options([

                                'Baru' => 'Baru',

                                'Second' => 'Second',

                            ]),




                        DatePicker::make('TanggalBeli')

                            ->label('Tanggal Beli')

                            ->default(now())

                            ->live()

                            ->afterStateUpdated(function (Get $get, Set $set, $state) {


                                if (

                                    $get('WarrantyStatus') === 'Ya'

                                    && $state

                                ) {


                                    $set(

                                        'DateWarranty',

                                        Carbon::parse($state)

                                            ->addYears(

                                                (int) ($get('Garansi') ?? 0)

                                            )

                                            ->format('Y-m-d')

                                    );


                                }


                            }),




                        TextInput::make('Harga')

                            ->label('Harga')

                            ->numeric()

                            ->prefix('Rp'),





                        Select::make('IDVendor')

                            ->label('Vendor')

                            ->relationship(

                                'vendor',

                                'NamaVendor'

                            )

                            ->searchable()

                            ->preload(),






                        Select::make('WarrantyStatus')

                            ->label('Garansi')

                            ->options([

                                'Ya' => 'Ya',

                                'Tidak' => 'Tidak',

                            ])

                            ->default('Tidak')

                            ->live()



                            ->afterStateHydrated(function ($record, Set $set) {



                                if ($record) {



                                    $garansi = (int) ($record->Garansi ?? 0);



                                    $set(

                                        'WarrantyStatus',

                                        $garansi > 0

                                            ? 'Ya'

                                            : 'Tidak'

                                    );



                                    $set(

                                        'Garansi',

                                        $garansi

                                    );



                                } else {



                                    $set(

                                        'WarrantyStatus',

                                        'Tidak'

                                    );



                                    $set(

                                        'Garansi',

                                        0

                                    );



                                }



                            })



                            ->afterStateUpdated(function ($state, Set $set) {



                                if ($state === 'Tidak') {



                                    $set(

                                        'Garansi',

                                        0

                                    );



                                    $set(

                                        'DateWarranty',

                                        null

                                    );



                                }



                            }),






                        TextInput::make('Garansi')

                            ->label('Lama Garansi (Tahun)')

                            ->numeric()

                            ->default(0)

                            ->dehydrated(true)



                            ->dehydrateStateUsing(function ($state) {


                                return $state === null

                                    ? 0

                                    : (int) $state;


                            })



                            ->visible(fn (Get $get) =>

                                $get('WarrantyStatus') === 'Ya'

                            )



                            ->afterStateHydrated(function ($record, Set $set) {



                                if ($record) {



                                    $set(

                                        'Garansi',

                                        (int) ($record->Garansi ?? 0)

                                    );



                                }



                            })



                            ->live()



                            ->afterStateUpdated(function (Get $get, Set $set, $state) {



                                if (!$get('TanggalBeli')) {

                                    return;

                                }



                                $set(

                                    'DateWarranty',

                                    Carbon::parse($get('TanggalBeli'))

                                        ->addYears(

                                            (int) ($state ?? 0)

                                        )

                                        ->format('Y-m-d')

                                );



                            }),






                        DatePicker::make('DateWarranty')

                            ->label('Tanggal Berakhir Garansi')

                            ->visible(fn(Get $get) =>

                                $get('WarrantyStatus') === 'Ya'

                            ),



                    ]),







                Section::make('Pengguna & Perusahaan')

                    ->columns(2)

                    ->schema([




                        Select::make('NIK')

                            ->label('Pemegang Asset')

                            ->relationship(

                                'karyawan',

                                'Nama'

                            )

                            ->searchable()

                            ->preload()

                            ->placeholder('Belum ada pemegang'),






                        Select::make('IDLokasi')

                            ->label('Lokasi Asset')

                            ->relationship(

                                'lokasi',

                                'NamaLokasi'

                            )

                            ->searchable()

                            ->preload()

                            ->placeholder('Belum ada lokasi'),






                        Select::make('IDPerusahaan')

                            ->label('Perusahaan')

                            ->relationship(

                                'perusahaan',

                                'NamaPerusahaan'

                            )

                            ->searchable()

                            ->preload()

                            ->required(),






                        Select::make('StatusAsset')

                            ->label('Status Asset')

                            ->options([


                                'Available' => 'Available',

                                'In Service' => 'In Service',

                                'Retired' => 'Retired',

                                'Not Used' => 'Not Used',


                            ])

                            ->default('Available')

                            ->required(),



                    ]),



            ]);

    }

}