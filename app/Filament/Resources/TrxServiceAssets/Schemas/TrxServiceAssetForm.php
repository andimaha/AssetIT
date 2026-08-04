<?php

namespace App\Filament\Resources\TrxServiceAssets\Schemas;

use App\Models\MstAsset;
use App\Models\MstVendor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Filament\Forms\Components\CurrencyInput;

class TrxServiceAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('NoAssetIT')
                    ->label('Asset')
                    ->options(fn () => MstAsset::orderBy('NoAssetIT')
                        ->get()
                        ->mapWithKeys(fn ($asset) => [
                            $asset->NoAssetIT => $asset->NoAssetIT . ' - ' . $asset->Nama,
                        ]))
                    ->searchable()
                    ->required(),

                DatePicker::make('TanggalMasuk')
                    ->default(now())
                    ->format('Y-m-d')
                    ->displayFormat('d M Y')
                    ->required(),

                DatePicker::make('TanggalSelesai')
                    ->format('Y-m-d')
                    ->displayFormat('d M Y'),

                Select::make('JenisService')
                    ->label('Jenis Service')
                    ->options([
                        'Maintenance' => 'Maintenance',
                        'Perbaikan' => 'Perbaikan',
                        'Upgrade' => 'Upgrade',
                    ])
                    ->required(),

                Textarea::make('Kerusakan')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('Tindakan')
                    ->columnSpanFull(),

                CurrencyInput::make('Biaya')
                    ->label('Biaya')
                    ->required(),

                Select::make('IDVendor')
                    ->label('Vendor Service')
                    ->options(fn () => MstVendor::orderBy('NamaVendor')
                        ->pluck('NamaVendor', 'IDVendor'))
                    ->searchable()
                    ->preload(),

                Select::make('StatusService')
                    ->label('Status Service')
                    ->options([
                        'Proses' => 'Proses',
                        'Selesai' => 'Selesai',
                        'Unrepairable' => 'Unrepairable',
                    ])
                    ->default('Proses')
                    ->required(),

                TextInput::make('Oleh')
                    ->label('Teknisi IT'),

            ]);
    }
}