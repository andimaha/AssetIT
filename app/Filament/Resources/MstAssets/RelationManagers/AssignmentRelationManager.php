<?php

namespace App\Filament\Resources\MstAssets\RelationManagers;

use App\Models\MstSoftwareLicense;
use Filament\Actions\Action;
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
    protected static string $relationship = 'softwareAssignment';



    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('IDLicense')
                    ->label('Software License')
                    ->options(
                        MstSoftwareLicense::with('software')
                            ->get()
                            ->mapWithKeys(function ($license) {

                                $software = $license->software?->NamaSoftware ?? '-';
                                $kode = $license->software?->KodeSoftwareCustom ?? '-';
                                $key = $license->ProductKey ?: '-';

                                return [
                                    $license->IDLicense =>
                                        "{$software} | {$license->IDLicense} | {$key}"
                                ];
                            })
                    )
                    ->searchable()
                    ->preload()
                    ->required(),


                DatePicker::make('TanggalAssign')
    ->label('Tanggal Assign')
    ->required()
    ->displayFormat('d M Y')
    ->format('Y-m-d'),


                DatePicker::make('TanggalRevoke')
                    ->label('Tanggal Revoke')
                    ->format('Y-m-d') ->displayFormat('d M Y'),


                Select::make('StatusAssignment')
                    ->label('Status')
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

            ->recordTitleAttribute('IDAssignment')

            ->columns([

            TextColumn::make('license.IDLicense')
    ->label('LICENSE ID')
    ->sortable()
    ->searchable(),


                TextColumn::make('license.software.NamaSoftware')
                    ->label('SOFTWARE')
                    ->badge()
                    ->sortable()
                    ->searchable(),



                TextColumn::make('license.ProductKey')
                    ->label('PRODUCT KEY')
                    ->formatStateUsing(fn () => '••••••••••••••••')
                    ->copyable(false),



                TextColumn::make('license.TipeLisensi')
                    ->label('LICENSE TYPE')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ?: '-')
                    ->color(fn ($state) => $state ? 'info' : 'gray'),



                TextColumn::make('TanggalAssign')
                    ->label('ASSIGNED')
                    ->date('d M Y')
                    ->sortable(),



                TextColumn::make('TanggalRevoke')
                    ->label('REVOKED')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable(),



                BadgeColumn::make('StatusAssignment')
                    ->label('STATUS')
                    ->colors([
                        'success' => 'Installed',
                        'danger' => 'Revoked',
                        'warning' => 'Expired',
                    ]),

            ])



            ->filters([])



            ->headerActions([
                CreateAction::make(),
            ])




            ->recordActions([


                Action::make('viewLicense')
                    ->label('Product Key')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->slideOver()
                    ->modalWidth('lg')
                    ->modalHeading(fn ($record) =>
                        'Product Key - ' .
                        ($record->license?->software?->NamaSoftware ?? '-')
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn ($record) =>
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