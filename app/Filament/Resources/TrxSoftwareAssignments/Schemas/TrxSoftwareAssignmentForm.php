<?php

namespace App\Filament\Resources\TrxSoftwareAssignments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TrxSoftwareAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([

                Select::make('NoAssetIT')

                    ->label('Asset')

                    ->relationship(
                        'asset',
                        'NoAssetIT'
                    )

                    ->getOptionLabelFromRecordUsing(
                        function ($record) {

                            return

                                ($record->NoAssetIT ?? '-')
                                . ' | '
                                . ($record->NoAssetSAP ?? '-')
                                . ' | '
                                . ($record->Nama ?? '-')
                                . ' | '
                                . ($record->perusahaan?->NamaPerusahaan ?? '-');

                        }
                    )

                    ->searchable([

                        'NoAssetIT',

                        'NoAssetSAP',

                        'Nama',

                    ])

                    ->preload()

                    ->required(),


                Select::make('IDLicense')

                    ->label('Software License')

                    ->relationship(
                        'license',
                        'IDLicense'
                    )

                    ->getOptionLabelFromRecordUsing(
                        function ($record) {

                            $software =
                                $record->software?->NamaSoftware
                                ??
                                '-';

                            $key =
                                $record->ProductKey
                                ?:
                                '-';

                            return

                                "{$software} | {$record->IDLicense} | {$key}";

                        }
                    )

                    ->searchable([

                        'IDLicense',

                        'ProductKey',

                        'software.NamaSoftware',

                    ])

                    ->preload()

                    ->required(),


                DatePicker::make('TanggalAssign')

                    ->label('Tanggal Assign')

                    ->default(now())

                    ->required()

                    ->format('Y-m-d')

                    ->displayFormat('d M Y'),


                DatePicker::make('TanggalRevoke')

                    ->label('Tanggal Revoke')

                    ->format('Y-m-d')

                    ->displayFormat('d M Y'),


                Select::make('StatusAssignment')

                    ->label('Status Assignment')

                    ->options([

                        'Installed' => 'Installed',

                        'Revoked' => 'Revoked',

                        'Expired' => 'Expired',

                    ])

                    ->default('Installed')

                    ->required(),

            ]);
    }
}
