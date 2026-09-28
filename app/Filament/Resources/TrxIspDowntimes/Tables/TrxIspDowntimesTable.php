<?php

namespace App\Filament\Resources\TrxIspDowntimes\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

use Filament\Forms\Components\DateTimePicker;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

use Illuminate\Database\Eloquent\Builder;

class TrxIspDowntimesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->recordTitleAttribute('NoTiket')

            ->columns([
                /*
                 * Perusahaan
                 *
                 * Relasi:
                 * TrxIspDowntime
                 *      -> isp
                 *          -> perusahaan
                 */
                TextColumn::make(
                    'isp.perusahaan.NamaPerusahaan'
                )
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable(),

                /*
                 * Vendor
                 */
                TextColumn::make(
                    'isp.vendor.NamaVendor'
                )
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),

                /*
                 * ISP
                 */
                TextColumn::make(
                    'isp.NamaISP'
                )
                    ->label('ISP')
                    ->searchable()
                    ->sortable(),

                /*
                 * Connection Type
                 */
                TextColumn::make(
                    'isp.ConnectionType'
                )
                    ->label('Connection Type')
                    ->searchable()
                    ->sortable(),

                /*
                 * Media Type
                 */
                TextColumn::make(
                    'isp.MediaType'
                )
                    ->label('Media Type')
                    ->searchable()
                    ->sortable(),

                

                /*
                 * Lokasi
                 */
                TextColumn::make(
                    'isp.lokasi.NamaLokasi'
                )
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),

                /*
                 * Nomor Tiket
                 */
                TextColumn::make('NoTiket')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                /*
                 * Tanggal Mulai
                 */
                TextColumn::make('TanggalMulai')
                    ->label('Mulai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                /*
                 * Tanggal Selesai
                 */
                TextColumn::make('TanggalSelesai')
                    ->label('Selesai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder(
                        'Masih berlangsung'
                    ),

                /*
                 * Total Jam
                 */
                TextColumn::make('TotalJam')
                    ->label('Total')
                    ->numeric(
                        decimalPlaces: 2
                    )
                    ->suffix(' jam')
                    ->sortable()
                    ->placeholder('-'),

                /*
                 * Lokasi Putus
                 */
                TextColumn::make('LokasiPutus')
                    ->label('Lokasi Putus')
                    ->limit(40)
                    ->tooltip(
                        fn ($record) =>
                            $record->LokasiPutus
                    )
                    ->searchable()
                    ->toggleable(),

                /*
                 * Penyebab
                 */
                TextColumn::make('Penyebab')
                    ->label('Penyebab')
                    ->limit(50)
                    ->tooltip(
                        fn ($record) =>
                            $record->Penyebab
                    )
                    ->searchable()
                    ->toggleable(),

                /*
                 * Dampak
                 */
                TextColumn::make('Dampak')
                    ->label('Dampak')
                    ->limit(50)
                    ->tooltip(
                        fn ($record) =>
                            $record->Dampak
                    )
                    ->searchable()
                    ->toggleable(),

                /*
                 * Status
                 */
                TextColumn::make('StatusDowntime')
                    ->label('Status')
                    ->state(
                        fn ($record) =>
                            $record->TanggalSelesai
                                ? 'Selesai'
                                : 'Berlangsung'
                    )
                    ->badge()
                    ->color(
                        fn ($state) =>
                            $state === 'Selesai'
                                ? 'success'
                                : 'danger'
                    ),

                /*
                 * Created At
                 */
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
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
                 * TrxIspDowntime
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
                 * Filter Downtime Sedang Berlangsung
                 */
                Filter::make('sedang_berlangsung')
                    ->label('Sedang Berlangsung')
                    ->query(
                        fn (Builder $query) =>
                            $query->whereNull(
                                'TanggalSelesai'
                            )
                    ),

                /*
                 * Filter Downtime Sudah Selesai
                 */
                Filter::make('sudah_selesai')
                    ->label('Sudah Selesai')
                    ->query(
                        fn (Builder $query) =>
                            $query->whereNotNull(
                                'TanggalSelesai'
                            )
                    ),

                /*
                 * Filter Periode
                 */
                Filter::make('periode')
                    ->label('Periode')
                    ->form([

                        DateTimePicker::make('mulai')
                            ->label('Mulai')
                            ->displayFormat(
                                'd M Y H:i'
                            )
                            ->format(
                                'Y-m-d H:i'
                            ),

                        DateTimePicker::make('selesai')
                            ->label('Selesai')
                            ->displayFormat(
                                'd M Y H:i'
                            )
                            ->format(
                                'Y-m-d H:i'
                            ),

                    ])
                    ->query(
                        function (
                            Builder $query,
                            array $data
                        ): Builder {

                            return $query

                                ->when(
                                    $data['mulai'] ?? null,
                                    fn (
                                        Builder $query,
                                        $date
                                    ) =>
                                        $query->where(
                                            'TanggalMulai',
                                            '>=',
                                            $date
                                        )
                                )

                                ->when(
                                    $data['selesai'] ?? null,
                                    fn (
                                        Builder $query,
                                        $date
                                    ) =>
                                        $query->where(
                                            'TanggalMulai',
                                            '<=',
                                            $date
                                        )
                                );
                        }
                    ),

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
                'TanggalMulai',
                'desc'
            );
    }
}
