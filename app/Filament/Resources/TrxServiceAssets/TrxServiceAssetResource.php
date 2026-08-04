<?php

namespace App\Filament\Resources\TrxServiceAssets;

use App\Filament\Resources\TrxServiceAssets\Pages\CreateTrxServiceAsset;
use App\Filament\Resources\TrxServiceAssets\Pages\EditTrxServiceAsset;
use App\Filament\Resources\TrxServiceAssets\Pages\ListTrxServiceAssets;
use App\Filament\Resources\TrxServiceAssets\Schemas\TrxServiceAssetForm;
use App\Filament\Resources\TrxServiceAssets\Tables\TrxServiceAssetsTable;
use App\Models\TrxServiceAsset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrxServiceAssetResource extends Resource
{
    protected static ?string $model = TrxServiceAsset::class;

    protected static ?string $navigationLabel = 'Service';

protected static ?string $modelLabel = 'Service';

protected static ?string $pluralModelLabel = 'Service';

protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    public static function form(Schema $schema): Schema
    {
        return TrxServiceAssetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrxServiceAssetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrxServiceAssets::route('/'),
            'create' => CreateTrxServiceAsset::route('/create'),
            'edit' => EditTrxServiceAsset::route('/{record}/edit'),
        ];
    }
}
