<?php

namespace App\Filament\Resources\TrxSoftwareAssignments\Tables;


use App\Filament\Resources\TrxSoftwareAssignments\TrxSoftwareAssignmentResource;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;



class TrxSoftwareAssignmentsTable
{


    public static function configure(Table $table): Table
    {


        return $table


            ->recordUrl(
                fn($record) =>
                TrxSoftwareAssignmentResource::getUrl(
                    'edit',
                    [
                        'record' => $record,
                    ]
                )
            )



            ->defaultSort(
                'TanggalAssign',
                'desc'
            )



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

                    'asset.karyawan.departemen',

                    'asset.perusahaan',

                    'license.software',

                    'license.perusahaan',

                ]);

            })



            ->columns([



                TextColumn::make('No')

                    ->label('NO')

                    ->rowIndex()

                    ->weight('bold'),






                TextColumn::make('asset.NoAssetIT')

                    ->label('ASSET')

                    ->formatStateUsing(function ($state, $record) {


                        return

                            ($record->asset?->NoAssetIT ?? '-')

                            .

                            ' | '

                            .

                            ($record->asset?->Nama ?? '-');


                    })

                    ->searchable(
                        query: function ($query, string $search): void {

                            $query->whereHas('asset', function ($query) use ($search) {

                                $query->where(function ($query) use ($search) {

                                    $query->where(
                                        'NoAssetIT',
                                        'like',
                                        "%{$search}%"
                                    )

                                    ->orWhere(
                                        'Nama',
                                        'like',
                                        "%{$search}%"
                                    );

                                });

                            });

                        }
                    )

                    ->sortable()

                    ->wrap(),






                TextColumn::make('asset.karyawan.Nama')

                    ->label('PEMEGANG ASSET')

                    ->placeholder('-')

                    ->searchable()

                    ->sortable(),






                TextColumn::make('asset.karyawan.departemen.NamaDept')

                    ->label('DEPARTEMEN')

                    ->placeholder('-')

                    ->badge()

                    ->searchable()

                    ->sortable()

                    ->toggleable(),






                TextColumn::make('asset.perusahaan.NamaPerusahaan')

                    ->label('PERUSAHAAN ASSET')

                    ->placeholder('-')

                    ->badge()

                    ->color('info')

                    ->searchable()

                    ->sortable()

                    ->toggleable(),







                TextColumn::make('license.software.NamaSoftware')

                    ->label('SOFTWARE')

                    ->placeholder('-')

                    ->searchable()

                    ->sortable()

                    ->wrap(),


                TextColumn::make('license.IDLicense')

                    ->label('ID LICENSE')

                    ->placeholder('-')

                    ->searchable()

                    ->sortable()

                    ->wrap(),









                TextColumn::make('license.perusahaan.NamaPerusahaan')

                    ->label('PERUSAHAAN LICENSE')

                    ->placeholder('-')

                    ->badge()

                    ->color('primary')

                    ->searchable()

                    ->sortable()

                    ->toggleable(),








                TextColumn::make('license.TipeLisensi')

                    ->label('TIPE LISENSI')

                    ->badge()

                    ->color('warning')

                    ->sortable()

                    ->toggleable(),








                TextColumn::make('license.ProductKey')

                    ->label('PRODUCT KEY')

                    ->formatStateUsing(
                        fn() => '••••••••••••••'
                    )

                    ->copyable(false)

                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),








                TextColumn::make('TanggalAssign')

                    ->label('TANGGAL INSTALL')

                    ->date('d M Y')

                    ->sortable(),








                TextColumn::make('TanggalRevoke')

                    ->label('TANGGAL REVOKE')

                    ->date('d M Y')

                    ->placeholder('-')

                    ->sortable()

                    ->toggleable(),









                TextColumn::make('asset.StatusAsset')

                    ->label('STATUS ASSET')

                    ->badge()

                    ->color(fn(?string $state): string => match ($state) {


                        'Available' => 'success',

                        'In Service' => 'warning',

                        'Retired' => 'danger',

                        default => 'gray',


                    })

                    ->sortable()

                    ->toggleable(),









                TextColumn::make('StatusAssignment')

                    ->label('STATUS SOFTWARE')

                    ->badge()

                    ->sortable()

                    ->color(fn(?string $state): string => match ($state) {


                        'Installed' => 'success',

                        'Revoked' => 'danger',

                        'Expired' => 'warning',

                        default => 'gray',


                    }),



            ])




            ->filters([

                //

            ])




            ->recordActions([


                Action::make('viewLicense')

                    ->label('Product Key')

                    ->icon('heroicon-o-key')

                    ->color('warning')

                    ->slideOver()

                    ->modalHeading(
                        fn($record) =>
                        'Product Key - ' .
                        (
                            $record->license?->software?->NamaSoftware
                            ??
                            '-'
                        )
                    )

                    ->modalSubmitAction(false)

                    ->modalCancelActionLabel('Close')

                    ->modalContent(
                        fn($record) =>
                        view(
                            'filament.tables.columns.assignment-product-key',
                            [
                                'license' => $record->license,
                            ]
                        )
                    ),



                EditAction::make(),



                DeleteAction::make(),


            ])




            ->toolbarActions([


                BulkActionGroup::make([


                    DeleteBulkAction::make(),


                ]),


            ]);


    }


}
