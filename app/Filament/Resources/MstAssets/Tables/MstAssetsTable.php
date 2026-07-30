<?php

namespace App\Filament\Resources\MstAssets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class MstAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort(
                'TanggalBeli',
                'desc'
            )

            ->columns([


                TextColumn::make('NoAssetIT')
                    ->label('NO ASSET IT')
                    ->weight('bold')
                    ->copyable()
                    ->searchable()
                    ->sortable(),



                TextColumn::make('NoAssetSAP')
                    ->label('NO ASSET SAP')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),



                TextColumn::make('Jenis')
                    ->label('JENIS ASSET')
                    ->badge()
                    ->searchable()
                    ->sortable(),



                TextColumn::make('Nama')
    ->label('NAMA ASSET')
    ->searchable()
    ->sortable()
    ->wrap()
    ->extraAttributes([
        'style' => '
            max-width: 420px;
            min-width: 350px;
            white-space: normal;
            word-break: break-word;
        ',
    ]),



                TextColumn::make('PN')
                    ->label('PART NUMBER')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    )
                    ->searchable(),



                TextColumn::make('SN')
                    ->label('SERIAL NUMBER')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    )
                    ->searchable(),



                TextColumn::make('PN_LCD')
                    ->label('PN LCD')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),



                TextColumn::make('SN_LCD')
                    ->label('SN LCD')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),



                TextColumn::make('RAM')
                    ->label('RAM')
                    ->badge(),



                TextColumn::make('JenisOS')
                    ->label('OPERATING SYSTEM')
                    ->badge()
                    ->toggleable(),



                TextColumn::make('ComputerName')
                    ->label('COMPUTER NAME')
                    ->searchable()
                    ->toggleable(),



                TextColumn::make('IPAddress')
                    ->label('IP ADDRESS')
                    ->toggleable(),



                TextColumn::make('Lapor')
                    ->label('LAPOR')
                    ->placeholder('-')
                    ->toggleable(),




                TextColumn::make('StatusBeli')
                    ->label('STATUS PEMBELIAN')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {

                        'Baru' => 'success',

                        'Second' => 'warning',

                        default => 'gray',

                    }),




                TextColumn::make('TanggalBeli')
                    ->label('TANGGAL BELI')
                    ->date('d M Y')
                    ->sortable(),




                TextColumn::make('Harga')
                    ->label('HARGA')
                    ->money(
                        'IDR',
                        locale: 'id'
                    )
                    ->sortable(),




                TextColumn::make('vendor.NamaVendor')
                    ->label('VENDOR')
                    ->searchable()
                    ->sortable(),




                TextColumn::make('Garansi')
                    ->label('GARANSI')
                    ->formatStateUsing(function ($state) {

                        return ($state && $state > 0)

                            ? $state . ' Tahun'

                            : 'Tidak Ada';

                    })
                    ->badge()
                    ->color(fn($state) =>

                        ($state && $state > 0)

                            ? 'success'

                            : 'danger'

                    ),




                TextColumn::make('DateWarranty')
                    ->label('BERAKHIR GARANSI')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable(),




                TextColumn::make('perusahaan.NamaPerusahaan')
                    ->label('PERUSAHAAN')
                    ->searchable()
                    ->sortable(),




                TextColumn::make('karyawan.Nama')
                    ->label('PEMEGANG ASSET')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),




                TextColumn::make('karyawan.Departemen.NamaDept')
                    ->label('DEPT')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),




                TextColumn::make('lokasi.NamaLokasi')
                    ->label('LOKASI ASSET')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),




                TextColumn::make('StatusAsset')
                    ->label('STATUS ASSET')
                    ->badge()
                    ->searchable()
                    ->color(fn(string $state) => match ($state) {

                        'Available' => 'success',

                        'In Service' => 'warning',

                        'Retired' => 'danger',

                        default => 'gray',

                    }),




                TextColumn::make('Keterangan')
                    ->label('KETERANGAN')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->extraAttributes([
                        'style' => '
                            min-width: 400px;
                            max-width: 500px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ]),


            ])




            ->filters([
                //
            ])




            ->recordActions([


                EditAction::make(),



                DeleteAction::make()

                    ->before(function ($record) {

                        $record->softwareAssignment()->delete();

                        $record->service()->delete();

                        $record->retire()->delete();

                    }),


            ])




            ->toolbarActions([


                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),


            ]);
    }
}