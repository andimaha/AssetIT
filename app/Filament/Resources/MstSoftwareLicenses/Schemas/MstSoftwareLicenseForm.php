<?php

namespace App\Filament\Resources\MstSoftwareLicenses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;


class MstSoftwareLicenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([


                TextInput::make('IDLicense')
                    ->label('ID License')
                    ->placeholder('Contoh: LIC-OFFICE-001')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),



                Select::make('IDSoftware')

                    ->label('Software')

                    ->relationship(
                        'software',
                        'NamaSoftware'
                    )

                    ->searchable()

                    ->preload()

                    ->required(),




                Select::make('IDPerusahaan')

                    ->label('Perusahaan')

                    ->relationship(
                        'perusahaan',
                        'NamaPerusahaan'
                    )

                    ->searchable()

                    ->preload()

                    ->required(),





                Textarea::make('ProductKey')

                    ->label('Product Key')

                    ->placeholder(
                        'Masukkan Product Key / Serial Number'
                    )

                    ->rows(4)

                    ->columnSpanFull(),





                Select::make('TipeLisensi')

                    ->label('Tipe Lisensi')

                    ->options([

                        'OEM' =>
                            'OEM',

                        'Retail' =>
                            'Retail',

                        'OLP' =>
                            'OLP',

                        'Volume' =>
                            'Volume',

                        'Subscription' =>
                            'Subscription',

                    ])

                    ->searchable()

                    ->required(),





                TextInput::make('JumlahLisensi')

                    ->label('Jumlah Lisensi')

                    ->numeric()

                    ->minValue(1)

                    ->default(1)

                    ->required(),





                Toggle::make('HasDVD')

                    ->label('Memiliki DVD / Installer')

                    ->inline(false),





                TextInput::make('Barcode')

                    ->label('Barcode')

                    ->placeholder(
                        'Barcode license'
                    )

                    ->maxLength(255),





                TextInput::make('LokasiSimpan')

                    ->label('Lokasi Penyimpanan')

                    ->placeholder(
                        'Contoh: Lemari IT'
                    )

                    ->maxLength(255),





                TextInput::make('TempatSimpan')

                    ->label('Tempat Penyimpanan')

                    ->placeholder(
                        'Contoh: Rak A-01'
                    )

                    ->maxLength(255),





                Textarea::make('Keterangan')

                    ->label('Keterangan')

                    ->rows(3)

                    ->columnSpanFull(),





                Select::make('StatusLisensi')

                    ->label('Status Lisensi')

                    ->options([

                        'Active' =>
                            'Active',

                        'Expired' =>
                            'Expired',

                        'Inactive' =>
                            'Inactive',

                    ])

                    ->default('Active')

                    ->required(),


            ]);
    }
}