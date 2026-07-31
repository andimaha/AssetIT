<?php

namespace App\Filament\Resources\MstAssets\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

use Filament\Forms\Components\DatePicker;
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
                    ->preload()
                    ->required(),



                TextInput::make('NoTransferSAP')
                    ->label('No Transfer SAP'),



                DatePicker::make('TanggalMutasi')
                    ->label('Tanggal Mutasi')
                    ->default(now())
                    ->displayFormat('d M Y')
                    ->format('Y-m-d') 
                    ->required(),



                TextInput::make('AksesWebsite'),


                TextInput::make('AksesEmail'),


                TextInput::make('Keterangan')
                    ->label('Keterangan'),

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
                    ->label('Lokasi')
                    ->searchable(),



                TextColumn::make('TanggalMutasi')
                    ->label('Tanggal Mutasi')
                    ->date('d M Y')
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


                        $this->updateLatestMutation($asset);



                        $livewire->dispatch(
                            'refreshAssetForm'
                        );


                    }),


            ])






            ->recordActions([



                EditAction::make()

                    ->after(function ($record, RelationManager $livewire) {


                        $asset = $livewire->getOwnerRecord();


                        $this->updateLatestMutation($asset);



                        $livewire->dispatch(
                            'refreshAssetForm'
                        );


                    }),





                DeleteAction::make()

                    ->after(function ($record, RelationManager $livewire) {


                        $asset = $livewire->getOwnerRecord();


                        $this->updateLatestMutation($asset);



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






    /**
     * Update pemegang dan lokasi berdasarkan
     * mutasi dengan TanggalMutasi TERBARU
     */
    private function updateLatestMutation($asset): void
    {


        $lastMutation = $asset

            ->mutasiAsset()

            ->orderByDesc('TanggalMutasi')

            ->first();



        if ($lastMutation) {


            $asset->update([

                'NIK' => $lastMutation->NIK,

                'IDLokasi' => $lastMutation->IDLokasi,

            ]);


        } else {


            $asset->update([

                'NIK' => null,

                'IDLokasi' => null,

            ]);


        }


    }


}