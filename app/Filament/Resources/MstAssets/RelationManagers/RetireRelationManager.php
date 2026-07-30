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
use Filament\Forms\Components\Textarea;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RetireRelationManager extends RelationManager
{
    protected static string $relationship = 'retire';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('NoRetireSAP')
                    ->label('No Retire SAP'),

                DateTimePicker::make('TanggalRetire')
                    ->default(now())
                    ->required(),

                Select::make('AlasanRetire')
                    ->options([
                        'Rusak Total' => 'Rusak Total',
                        'Hilang' => 'Hilang',
                        'Dijual' => 'Dijual',
                        'Hibah' => 'Hibah',
                    ])
                    ->required(),

                Textarea::make('KeteranganDetail')
                    ->columnSpanFull(),

                TextInput::make('EksekutorIT')
                    ->label('Eksekutor IT'),

                TextInput::make('NilaiSisa')
                    ->label('Nilai Sisa')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('AlasanRetire')

            ->columns([

                TextColumn::make('NoRetireSAP')
                    ->label('No Retire SAP')
                    ->searchable(),

                TextColumn::make('TanggalRetire')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('AlasanRetire')
                    ->badge(),

                TextColumn::make('EksekutorIT')
                    ->label('Eksekutor IT'),

                TextColumn::make('NilaiSisa')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('KeteranganDetail')
    ->label('Keterangan')
    ->limit(50)
    ->tooltip(fn ($record) => $record->KeteranganDetail)
    ->wrap()
    ->searchable(),

            ])

            ->headerActions([

                CreateAction::make()
                    ->after(function ($record, RelationManager $livewire) {

                        $livewire->getOwnerRecord()->update([
                            'StatusAsset' => 'Retired',
                        ]);

                        $livewire->dispatch('refreshAssetForm');

                    }),

            ])

            ->recordActions([

                EditAction::make()
                    ->after(function ($record, RelationManager $livewire) {

                        $livewire->getOwnerRecord()->update([
                            'StatusAsset' => 'Retired',
                        ]);
                        $livewire->dispatch('refreshAssetForm');

                    }),

                DeleteAction::make(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),

            ]);
    }
}