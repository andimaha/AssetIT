<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TrxRetireAsset extends Model
{

    protected $table='trxretireasset';


    protected $primaryKey='IDRetire';


    public $timestamps=false;



    protected $fillable = [
    'NoAssetIT',
    'NoRetireSAP',
    'TanggalRetire',
    'AlasanRetire',
    'Kondisi',
    'KeteranganDetail',
    'EksekutorIT',
    'NilaiSisa'
];



    protected $casts=[
        'TanggalRetire'=>'datetime',
        'NilaiSisa'=>'decimal:2'
    ];



    public function asset()
{
    return $this->belongsTo(
        MstAsset::class,
        'NoAssetIT',
        'NoAssetIT'
    );
}

}