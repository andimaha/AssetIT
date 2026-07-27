<?php

namespace App\Filament\Widgets;

use App\Models\MstAsset;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class WarrantyExpiringAssets extends BaseWidget
{
    protected static ?string $heading = 'Warranty Akan Habis';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MstAsset::query()
                    ->whereNotNull('DateWarranty')
                    ->whereDate('DateWarranty', '>=', now()) // Belum expired
                    ->whereDate('DateWarranty', '<=', now()->addMonth()) // Maksimal 1 bulan lagi
                    ->orderBy('DateWarranty', 'asc') // Yang paling dekat habis di atas
            )

            ->columns([
                Tables\Columns\TextColumn::make('NoAssetIT')
                    ->label('No Asset')
                    ->searchable(),

                Tables\Columns\TextColumn::make('Nama')
                    ->label('Nama Asset')
                    ->searchable(),

                Tables\Columns\TextColumn::make('perusahaan.NamaPerusahaan')
                    ->label('Perusahaan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('DateWarranty')
                    ->label('Warranty')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('DateWarranty')
                    ->label('Sisa Hari')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        now()->diffInDays($record->DateWarranty, false) <= 7 => 'danger',
                        now()->diffInDays($record->DateWarranty, false) <= 14 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => now()->diffInDays($state) . ' Hari Lagi'),
            ]);
    }
}