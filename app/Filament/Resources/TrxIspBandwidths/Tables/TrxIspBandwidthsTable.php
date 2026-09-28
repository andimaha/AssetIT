<?php

namespace App\Filament\Resources\TrxIspBandwidths\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class TrxIspBandwidthsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->recordTitleAttribute('TanggalUpgrade')

            ->columns([
                
                /*
                 * Perusahaan
                 * Relasi:
                 * TrxIspBandwidth -> isp -> perusahaan
                 */
                TextColumn::make('isp.perusahaan.NamaPerusahaan')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable(),

                /*
                 * Vendor
                 */
                TextColumn::make('isp.vendor.NamaVendor')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),

                

                /*
                 * ISP
                 */
                TextColumn::make('isp.NamaISP')
                    ->label('ISP')
                    ->searchable()
                    ->sortable(),

                /*
                 * Connection Type
                 */
                TextColumn::make('isp.ConnectionType')
                    ->label('Connection Type')
                    ->searchable()
                    ->sortable(),

                /*
                 * Media Type
                 */
                TextColumn::make('isp.MediaType')
                    ->label('Media Type')
                    ->searchable()
                    ->sortable(),


                /*
                 * Lokasi
                 */
                TextColumn::make('isp.lokasi.NamaLokasi')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),

                /*
                 * Tanggal Upgrade
                 */
                TextColumn::make('TanggalUpgrade')
                    ->label('Tanggal Upgrade')
                    ->date('d/m/Y')
                    ->sortable(),

                /*
                 * Bandwidth Internasional
                 */
                TextColumn::make('BandwidthInternasional')
                    ->label('Internasional')
                    ->suffix(' Mbps')
                    ->sortable(),

                /*
                 * Bandwidth Lokal
                 */
                TextColumn::make('BandwidthLokal')
                    ->label('Lokal')
                    ->suffix(' Mbps')
                    ->sortable(),

                /*
                 * Harga
                 */
                TextColumn::make('Harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                /*
                 * Status
                 */
                TextColumn::make('Status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'ACTIVE' => 'success',
                            'INACTIVE' => 'gray',
                            default => 'gray',
                        }
                    ),

                /*
                 * Keterangan
                 */
                TextColumn::make('Keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->tooltip(
                        fn ($record) => $record->Keterangan
                    ),

                /*
                 * Created At
                 */
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                /*
                 * Filter ISP
                 */
                SelectFilter::make('IDISP')
                    ->label('ISP')
                    ->relationship(
                        'isp',
                        'NamaISP'
                    )
                    ->searchable()
                    ->preload(),

                /*
                 * Filter Perusahaan
                 *
                 * Relasi:
                 * TrxIspBandwidth
                 *      -> isp
                 *          -> perusahaan
                 */
                SelectFilter::make('perusahaan')
                    ->label('Perusahaan')
                    ->relationship(
                        'isp.perusahaan',
                        'NamaPerusahaan'
                    )
                    ->searchable()
                    ->preload(),

                /*
                 * Filter Connection Type
                 */
                SelectFilter::make('connection_type')
                    ->label('Connection Type')
                    ->options(
                        fn () =>
                            \App\Models\MstIsp::query()
                                ->whereNotNull(
                                    'ConnectionType'
                                )
                                ->where(
                                    'ConnectionType',
                                    '!=',
                                    ''
                                )
                                ->distinct()
                                ->orderBy(
                                    'ConnectionType'
                                )
                                ->pluck(
                                    'ConnectionType',
                                    'ConnectionType'
                                )
                                ->toArray()
                    )
                    ->query(
                        fn (
                            $query,
                            array $data
                        ) =>
                            $query->when(
                                $data['value'] ?? null,
                                fn (
                                    $query,
                                    $value
                                ) =>
                                    $query->whereHas(
                                        'isp',
                                        fn (
                                            $ispQuery
                                        ) =>
                                            $ispQuery->where(
                                                'ConnectionType',
                                                $value
                                            )
                                    )
                            )
                    ),

                /*
                 * Filter Media Type
                 */
                SelectFilter::make('media_type')
                    ->label('Media Type')
                    ->options(
                        fn () =>
                            \App\Models\MstIsp::query()
                                ->whereNotNull(
                                    'MediaType'
                                )
                                ->where(
                                    'MediaType',
                                    '!=',
                                    ''
                                )
                                ->distinct()
                                ->orderBy(
                                    'MediaType'
                                )
                                ->pluck(
                                    'MediaType',
                                    'MediaType'
                                )
                                ->toArray()
                    )
                    ->query(
                        fn (
                            $query,
                            array $data
                        ) =>
                            $query->when(
                                $data['value'] ?? null,
                                fn (
                                    $query,
                                    $value
                                ) =>
                                    $query->whereHas(
                                        'isp',
                                        fn (
                                            $ispQuery
                                        ) =>
                                            $ispQuery->where(
                                                'MediaType',
                                                $value
                                            )
                                    )
                            )
                    ),

                /*
                 * Filter Lokasi
                 */
                SelectFilter::make('lokasi')
                    ->label('Lokasi')
                    ->relationship(
                        'isp.lokasi',
                        'NamaLokasi'
                    )
                    ->searchable()
                    ->preload(),

                /*
                 * Filter Status
                 */
                SelectFilter::make('Status')
                    ->label('Status')
                    ->options([
                        'ACTIVE' => 'Active',
                        'INACTIVE' => 'Inactive',
                    ])
                    ->default('ACTIVE'),

            ])

            ->recordActions([

                EditAction::make(),

                DeleteAction::make(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ])

            ->defaultSort(
                'TanggalUpgrade',
                'desc'
            );
    }
}
