<?php

namespace App\Filament\Resources\TrxMutasiAssets;

use App\Filament\Resources\TrxMutasiAssets\Pages\CreateTrxMutasiAsset;
use App\Filament\Resources\TrxMutasiAssets\Pages\EditTrxMutasiAsset;
use App\Filament\Resources\TrxMutasiAssets\Pages\ListTrxMutasiAssets;
use App\Filament\Resources\TrxMutasiAssets\Schemas\TrxMutasiAssetForm;
use App\Filament\Resources\TrxMutasiAssets\Tables\TrxMutasiAssetsTable;
use App\Models\TrxMutasiAsset;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;


class TrxMutasiAssetResource extends Resource
{
    protected static ?string $model = TrxMutasiAsset::class;


    protected static string|\UnitEnum|null $navigationGroup = 'Asset Management';


    protected static ?string $navigationLabel = 'Mutasi Asset';


    protected static ?string $modelLabel = 'Mutasi Asset';


    protected static ?string $pluralModelLabel = 'Mutasi Asset';


    protected static string|\BackedEnum|null $navigationIcon =
        Heroicon::OutlinedArrowsRightLeft;


    public static function form(Schema $schema): Schema
    {
        return TrxMutasiAssetForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return TrxMutasiAssetsTable::configure($table);
    }


    public static function getPages(): array
    {
        return [
            'index' => ListTrxMutasiAssets::route('/'),
            'create' => CreateTrxMutasiAsset::route('/create'),
            'edit' => EditTrxMutasiAsset::route('/{record}/edit'),
        ];
    }
}
