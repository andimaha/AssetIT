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

class ServiceRelationManager extends RelationManager
{
    protected static string $relationship = 'service';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                DateTimePicker::make('TanggalMasuk')
                    ->default(now())
                    ->required(),

                DateTimePicker::make('TanggalSelesai'),

                Select::make('JenisService')
                    ->options([
                        'Maintenance' => 'Maintenance',
                        'Perbaikan' => 'Perbaikan',
                        'Upgrade' => 'Upgrade',
                    ])
                    ->required(),

                Textarea::make('Kerusakan')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('Tindakan')
                    ->columnSpanFull(),

                TextInput::make('Biaya')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                Select::make('IDVendor')
                    ->label('Vendor Service')
                    ->relationship('vendor', 'NamaVendor')
                    ->searchable()
                    ->preload(),

                Select::make('StatusService')
                    ->options([
                        'Proses' => 'Proses',
                        'Selesai' => 'Selesai',
                        'Unrepairable' => 'Unrepairable',
                    ])
                    ->default('Proses')
                    ->required(),

                TextInput::make('Oleh')
                    ->label('Teknisi IT'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('JenisService')

            ->columns([

                TextColumn::make('TanggalMasuk')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('JenisService')
                    ->searchable(),

                TextColumn::make('Kerusakan')
                    ->limit(40),
                    
                TextColumn::make('Tindakan')
                    ->limit(40),

                TextColumn::make('vendor.NamaVendor')
                    ->label('Vendor'),

                TextColumn::make('Biaya')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('StatusService')
                    ->badge(),

                TextColumn::make('Oleh'),
            ])

            ->headerActions([

                CreateAction::make()
    ->after(function ($record, RelationManager $livewire) {

        $livewire->getOwnerRecord()->update([
            'StatusAsset' => 'In Service',
        ]);

        $livewire->dispatch('refreshAssetForm');
    }),

            ])

            ->recordActions([

                EditAction::make()
                    ->after(function ($record, RelationManager $livewire) {

                        if ($record->StatusService == 'Proses') {

                            $status = 'In Service';

                        } elseif ($record->StatusService == 'Selesai') {

                            $status = 'Available';

                        } else {

                            $status = 'Retired';

                        }

                        $livewire->getOwnerRecord()->update([
    'StatusAsset' => $status,
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