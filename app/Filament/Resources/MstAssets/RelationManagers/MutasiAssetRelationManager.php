<?php

namespace App\Filament\Resources\MstAssets\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Schemas\Schema;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class MutasiAssetRelationManager extends RelationManager
{

    protected static string $relationship = 'mutasiAsset';



    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([


                Select::make('NIK')
                    ->label('Karyawan')
                    ->relationship(
                        'karyawan',
                        'Nama'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),



                Select::make('IDLokasi')
                    ->label('Lokasi')
                    ->relationship(
                        'lokasi',
                        'NamaLokasi'
                    )
                    ->searchable()
                    ->preload(),



                TextInput::make('NoTransferSAP')
                    ->label('No Transfer SAP'),



                DateTimePicker::make('TanggalMutasi')
                    ->label('Tanggal Mutasi')
                    ->default(now())
                    ->required(),



                TextInput::make('AksesWebsite'),


                TextInput::make('AksesEmail'),


                TextInput::make('Keterangan'),

            ]);
    }







    public function table(Table $table): Table
    {
        return $table


            ->recordTitleAttribute('NIK')


            ->defaultSort(
                'TanggalMutasi',
                'desc'
            )


            ->columns([


                TextColumn::make('NIK')
                    ->label('NIK')
                    ->searchable(),



                TextColumn::make('karyawan.Nama')
                    ->label('Karyawan')
                    ->searchable(),



                TextColumn::make('karyawan.departemen.NamaDept')
                    ->label('Departemen'),



                TextColumn::make('lokasi.NamaLokasi')
                    ->label('Lokasi'),



                TextColumn::make('TanggalMutasi')
                    ->label('Tanggal Mutasi')
                    ->dateTime()
                    ->sortable(),
                
                TextColumn::make('Keterangan')
        ->label('Keterangan')
        ->placeholder('-')
        ->wrap()
        ->searchable(),


            ])







            ->headerActions([



                CreateAction::make()

                    ->after(function ($record, RelationManager $livewire) {


                        $asset = $livewire->getOwnerRecord();



                        // Ambil mutasi dengan tanggal paling akhir
                        $lastMutation = $asset
                            ->mutasiAsset()
                            ->orderByDesc('TanggalMutasi')
                            ->first();



                        $asset->update([

                            'NIK' => $lastMutation?->NIK,

                        ]);



                        $livewire->dispatch(
                            'refreshAssetForm'
                        );


                    }),



            ])









            ->recordActions([



                EditAction::make()

                    ->after(function ($record, RelationManager $livewire) {


                        $asset = $livewire->getOwnerRecord();



                        // Cari ulang berdasarkan tanggal mutasi terbaru
                        // bukan berdasarkan data yang diedit

                        $lastMutation = $asset
                            ->mutasiAsset()
                            ->orderByDesc('TanggalMutasi')
                            ->first();



                        $asset->update([

                            'NIK' => $lastMutation?->NIK,

                        ]);



                        $livewire->dispatch(
                            'refreshAssetForm'
                        );


                    }),







                DeleteAction::make()

                    ->after(function ($record, RelationManager $livewire) {


                        $asset = $livewire->getOwnerRecord();



                        // Setelah delete cari histori terakhir

                        $lastMutation = $asset
                            ->mutasiAsset()
                            ->orderByDesc('TanggalMutasi')
                            ->first();



                        $asset->update([

                            'NIK' => $lastMutation?->NIK,

                        ]);



                        $livewire->dispatch(
                            'refreshAssetForm'
                        );


                    }),



            ])









            ->toolbarActions([


                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),


            ]);


    }

}