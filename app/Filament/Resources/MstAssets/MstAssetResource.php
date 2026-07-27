<?php

namespace App\Filament\Resources\MstAssets;

use App\Filament\Resources\MstAssets\Pages\CreateMstAsset;
use App\Filament\Resources\MstAssets\Pages\EditMstAsset;
use App\Filament\Resources\MstAssets\Pages\ListMstAssets;

use App\Filament\Resources\MstAssets\Schemas\MstAssetForm;
use App\Filament\Resources\MstAssets\Tables\MstAssetsTable;

use App\Filament\Resources\MstAssets\RelationManagers\MutasiAssetRelationManager;
use App\Filament\Resources\MstAssets\RelationManagers\ServiceRelationManager;
use App\Filament\Resources\MstAssets\RelationManagers\RetireRelationManager;
use App\Filament\Resources\MstAssets\RelationManagers\AssignmentRelationManager;

use App\Models\MstAsset;

use BackedEnum;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;


class MstAssetResource extends Resource
{

    protected static ?string $model = MstAsset::class;


    protected static ?string $navigationLabel = 'Asset';


    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';


    protected static ?string $modelLabel = 'Asset';


    protected static ?string $pluralModelLabel = 'Asset';


    protected static string|\UnitEnum|null $navigationGroup = 'Asset Management';



    public static function form(Schema $schema): Schema
    {
        return MstAssetForm::configure($schema);
    }



    /**
     * Saat CREATE
     */
    public static function mutateFormDataBeforeCreate(array $data): array
    {

        if (
            !isset($data['Garansi'])
            || $data['Garansi'] === null
            || $data['Garansi'] === ''
        ) {

            $data['Garansi'] = 0;

        }


        return $data;
    }



    /**
     * Saat EDIT lalu SAVE
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {

        if (
            !isset($data['Garansi'])
            || $data['Garansi'] === null
            || $data['Garansi'] === ''
        ) {

            $data['Garansi'] = 0;

        }


        return $data;
    }



    public static function table(Table $table): Table
    {
        return MstAssetsTable::configure($table);
    }



    public static function getRelations(): array
    {
        return [
            MutasiAssetRelationManager::class,
            ServiceRelationManager::class,
            RetireRelationManager::class,
            AssignmentRelationManager::class,
        ];
    }



    public static function getPages(): array
    {
        return [

            'index' => ListMstAssets::route('/'),

            'create' => CreateMstAsset::route('/create'),

            'edit' => EditMstAsset::route('/{record}/edit'),

        ];
    }

}