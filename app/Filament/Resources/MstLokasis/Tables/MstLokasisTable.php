<?php

namespace App\Filament\Resources\MstLokasis\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;


class MstLokasisTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([


                TextColumn::make('NamaLokasi')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable(),



                TextColumn::make('Keterangan')
                    ->label('Keterangan')
                    ->placeholder('-')
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),


            ])


            ->actions([

                EditAction::make(),

                DeleteAction::make(),

            ]);
    }
}