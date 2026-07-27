<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstLokasi extends Model
{
    protected $table='mstlokasi';

    protected $primaryKey='IDLokasi';

    public $timestamps=false;

    protected $fillable=[
        'NamaLokasi',
        'Keterangan'
    ];


    public function mutasi()
    {
        return $this->hasMany(
            TrxMutasiAsset::class,
            'IDLokasi'
        );
    }
}