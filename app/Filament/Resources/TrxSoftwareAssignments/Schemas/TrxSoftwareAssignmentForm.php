<?php

namespace App\Filament\Resources\TrxSoftwareAssignments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TrxSoftwareAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('NoAssetIT')
                    ->label('Asset')
                    ->relationship('asset', 'NoAssetIT')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->NoAssetIT} - {$record->Nama}")
                    ->searchable(['NoAssetIT', 'Nama'])
                    ->preload()
                    ->required(),

                Select::make('IDLicense')
                    ->label('Software License')
                    ->relationship('license', 'ProductKey')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $software = $record->software?->NamaSoftware ?? '-';
                        $key = $record->ProductKey ?? '-';

                        return "{$software} | {$record->IDLicense} | {$key}";
                    })
                    ->searchable(['IDLicense', 'ProductKey'])
                    ->preload()
                    ->required(),

                DatePicker::make('TanggalAssign')
                    ->required(),

                DatePicker::make('TanggalRevoke'),

                Select::make('StatusAssignment')
                    ->options([
                        'Installed' => 'Installed',
                        'Revoked' => 'Revoked',
                        'Expired' => 'Expired',
                    ])
                    ->default('Installed')
                    ->required(),

                Textarea::make('Keterangan')
                    ->columnSpanFull(),

            ]);
    }
}