<?php

namespace App\Filament\Resources\TrxSoftwareAssignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrxSoftwareAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('asset.NoAssetIT')
                    ->label('Asset')
                    ->formatStateUsing(function ($state, $record) {
                        return "{$record->asset->NoAssetIT} | {$record->asset->Nama}";
                    })
                    ->searchable(['asset.NoAssetIT', 'asset.Nama']),

                TextColumn::make('license.IDLicense')
                    ->label('Software License')
                    ->formatStateUsing(function ($state, $record) {
                        $software = $record->license->software->NamaSoftware ?? '-';
                        $key = $record->license->ProductKey ?? '-';

                        return "{$software} | {$record->license->IDLicense}";
                    })
                    ->searchable([
                        'license.IDLicense',
                        'license.ProductKey',
                        'license.software.NamaSoftware',
                    ])
                    ->wrap(),

                TextColumn::make('license.TipeLisensi')
                    ->label('License Type')
                    ->sortable(),

                TextColumn::make('TanggalAssign')
                    ->label('Assign Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('TanggalRevoke')
                    ->label('Revoke Date')
                    ->date('d M Y')
                    ->sortable(),

                BadgeColumn::make('StatusAssignment')
                    ->colors([
                        'success' => 'Installed',
                        'warning' => 'Expired',
                        'danger' => 'Revoked',
                    ]),
            ])

            ->filters([
                //
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