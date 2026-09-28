<?php

namespace App\Filament\Resources\MstIsp\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MstIspTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([
                
                TextColumn::make('perusahaan.NamaPerusahaan')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ISPCode')
                    ->label('Kode ISP')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('ConnectionType')
                    ->label('Tipe Koneksi')
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'PRIMARY' => 'success',
                            'BACKUP' => 'warning',
                            default => 'gray',
                        }
                    )
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'PRIMARY' => 'Primary',
                            'BACKUP' => 'Backup',
                            default => '-',
                        }
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('NamaISP')
                    ->label('Nama Paket')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('vendor.NamaVendor')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('lokasi.NamaLokasi')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('MediaType')
                    ->label('Media')
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'Fiber Optic' => 'success',
                            'Wireless' => 'info',
                            'Radio' => 'warning',
                            'Satellite' => 'gray',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('ContractStart')
                    ->label('Mulai Kontrak')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('ContractEnd')
                    ->label('Berakhir Kontrak')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('SLA')
                    ->label('SLA')
                    ->suffix('%')
                    ->sortable(),

                IconColumn::make('IsActive')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                SelectFilter::make('ConnectionType')
                    ->label('Tipe Koneksi')
                    ->options([
                        'PRIMARY' => 'Primary',
                        'BACKUP' => 'Backup',
                    ]),

                SelectFilter::make('IDPerusahaan')
                    ->label('Perusahaan')
                    ->relationship(
                        name: 'perusahaan',
                        titleAttribute: 'NamaPerusahaan'
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('MediaType')
                    ->label('Media')
                    ->options([
                        'Fiber Optic' => 'Fiber Optic',
                        'Wireless' => 'Wireless',
                        'Radio' => 'Radio',
                        'Copper' => 'Copper',
                        'Satellite' => 'Satellite',
                        'Other' => 'Other',
                    ]),

                SelectFilter::make('IsActive')
                    ->label('Status Aktif')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),

            ])

            ->recordActions([

                /*
                 * View
                 */
                ViewAction::make(),

                /*
                 * Edit
                 */
                EditAction::make(),

                /*
                 * Delete
                 */
                DeleteAction::make(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ])

            ->defaultSort(
                'NamaISP'
            );
    }
}
