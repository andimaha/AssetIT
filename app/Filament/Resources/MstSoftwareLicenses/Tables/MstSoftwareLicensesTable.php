<?php

namespace App\Filament\Resources\MstSoftwareLicenses\Tables;

use App\Filament\Resources\MstSoftwareLicenses\MstSoftwareLicenseResource;
use App\Models\MstPerusahaan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MstSoftwareLicensesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->recordUrl(fn ($record) => MstSoftwareLicenseResource::getUrl('edit', [
                'record' => $record,
            ]))

            ->paginated([
                10,
                25,
                50,
                100,
                250,
                'all',
            ])

            ->paginationPageOptions([
                10,
                25,
                50,
                100,
                250,
                'all',
            ])

            ->defaultPaginationPageOption('all')

            ->modifyQueryUsing(function ($query) {
                $query->with([
                    'software',
                    'perusahaan',
                ]);
            })

            ->columns([

                TextColumn::make('No')
                    ->label('NO')
                    ->rowIndex()
                    ->weight('bold'),

                TextColumn::make('IDLicense')
                    ->label('ID LICENSE')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('software.NamaSoftware')
                    ->label('SOFTWARE')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('perusahaan.NamaPerusahaan')
                    ->label('PERUSAHAAN')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('TipeLisensi')
                    ->label('TIPE LISENSI')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('JumlahLisensi')
                    ->label('JUMLAH')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('assignment_count')
                    ->counts('assignment')
                    ->label('TERPAKAI')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state . ' Asset'),

                IconColumn::make('HasDVD')
                    ->label('DVD')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('Barcode')
                    ->label('BARCODE')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('LokasiSimpan')
                    ->label('LOKASI')
                    ->searchable(),

                TextColumn::make('TempatSimpan')
                    ->label('TEMPAT')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('StatusLisensi')
                    ->label('STATUS')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Expired' => 'danger',
                        'Inactive' => 'gray',
                        default => 'warning',
                    }),

            ])

            ->filters([

                SelectFilter::make('IDPerusahaan')
                    ->label('PERUSAHAAN')
                    ->relationship(
                        'perusahaan',
                        'NamaPerusahaan'
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('StatusLisensi')
                    ->label('STATUS')
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                        'Expired' => 'Expired',
                    ]),

            ])

            ->recordActions([

                EditAction::make(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}