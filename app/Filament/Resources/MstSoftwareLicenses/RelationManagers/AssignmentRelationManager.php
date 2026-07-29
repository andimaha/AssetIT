<?php

namespace App\Filament\Resources\MstSoftwareLicenses\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class AssignmentRelationManager extends RelationManager
{
    protected static string $relationship = 'assignment';


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('NoAssetIT')
                    ->label('Asset')
                    ->relationship('asset', 'NoAssetIT')
                    ->searchable()
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

            ]);
    }


    public function table(Table $table): Table
    {
        return $table

            ->recordTitleAttribute('NoAssetIT')

            ->columns([

                TextColumn::make('asset.NoAssetIT')
                    ->label('Asset')
                    ->searchable(),

                TextColumn::make('asset.Nama')
                    ->label('Nama Asset')
                    ->searchable(),

                TextColumn::make('TanggalAssign')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('TanggalRevoke')
                    ->date('d M Y')
                    ->sortable(),

                BadgeColumn::make('StatusAssignment')
                    ->colors([
                        'success' => 'Installed',
                        'danger' => 'Revoked',
                        'warning' => 'Expired',
                    ]),

            ])

            ->filters([
                //
            ])

            ->headerActions([
                CreateAction::make(),
            ])

            ->recordActions([
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