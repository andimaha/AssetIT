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
use Filament\Forms\Components\Textarea;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Forms\Components\CurrencyInput;

class ServiceRelationManager extends RelationManager
{
    protected static string $relationship = 'service';

    /**
     * Update Status Asset berdasarkan seluruh data service.
     */
    protected function updateAssetStatus(RelationManager $livewire): void
    {
        $asset = $livewire->getOwnerRecord();

        $services = $asset->service()->get();

        if ($services->where('StatusService', 'Proses')->isNotEmpty()) {
            $status = 'In Service';
        } elseif ($services->where('StatusService', 'Unrepairable')->isNotEmpty()) {
            $status = 'Retired';
        } else {
            $status = 'Available';
        }

        $asset->update([
            'StatusAsset' => $status,
        ]);

        $livewire->dispatch('refreshAssetForm');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                DatePicker::make('TanggalMasuk')
                    ->default(now())
                    ->format('Y-m-d') ->displayFormat('d M Y')
                    ->required(),

                DatePicker::make('TanggalSelesai')
                ->format('Y-m-d') ->displayFormat('d M Y'),

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

                CurrencyInput::make('Biaya')
                ->required()
                            ->label('Biaya'),

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
                    ->date('d M Y')
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
                    ->label('Biaya')
                    ->money(
                        'IDR',
                        locale: 'id'
                    )
                    ->sortable(),

                TextColumn::make('StatusService')
                    ->badge(),

                TextColumn::make('Oleh'),
            ])

            ->headerActions([

                CreateAction::make()
                    ->after(function ($record, RelationManager $livewire) {
                        $this->updateAssetStatus($livewire);
                    }),

            ])

            ->recordActions([

                EditAction::make()
                    ->after(function ($record, RelationManager $livewire) {
                        $this->updateAssetStatus($livewire);
                    }),

                DeleteAction::make()
                    ->after(function ($record, RelationManager $livewire) {
                        $this->updateAssetStatus($livewire);
                    }),

            ])

            ->toolbarActions([

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),

            ]);
    }
}