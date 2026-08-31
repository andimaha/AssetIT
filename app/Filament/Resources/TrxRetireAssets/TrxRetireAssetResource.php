<?php

namespace App\Filament\Resources\TrxRetireAssets;

use App\Filament\Resources\TrxRetireAssets\Pages\CreateTrxRetireAsset;
use App\Filament\Resources\TrxRetireAssets\Pages\EditTrxRetireAsset;
use App\Filament\Resources\TrxRetireAssets\Pages\ListTrxRetireAssets;
use App\Filament\Resources\TrxRetireAssets\Schemas\TrxRetireAssetForm;
use App\Filament\Resources\TrxRetireAssets\Tables\TrxRetireAssetsTable;
use App\Models\TrxRetireAsset;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;


class TrxRetireAssetResource extends Resource
{
    protected static ?string $model = TrxRetireAsset::class;


    protected static string|\UnitEnum|null $navigationGroup = 'Asset Management';


    protected static ?string $navigationLabel = 'Retire Asset';


    protected static ?string $modelLabel = 'Retire Asset';


    protected static ?string $pluralModelLabel = 'Retire Asset';




    public static function form(Schema $schema): Schema
    {
        return TrxRetireAssetForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return TrxRetireAssetsTable::configure($table);
    }


    public static function getPages(): array
    {
        return [
            'index' => ListTrxRetireAssets::route('/'),
            'create' => CreateTrxRetireAsset::route('/create'),
            'edit' => EditTrxRetireAsset::route('/{record}/edit'),
        ];
    }
}
