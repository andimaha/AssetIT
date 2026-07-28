<?php

namespace App\Models;
use App\Models\TrxMutasiAsset;

use Illuminate\Database\Eloquent\Model;


class MstAsset extends Model
{
    protected $table = 'mstasset';

    protected $primaryKey = 'NoAssetIT';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'NoAssetIT',
        'NoAssetSAP',
        'Jenis',
        'Nama',
        'PN',
        'SN',
        'PN_LCD',
        'SN_LCD',
        'RAM',
        'JenisOS',
        'ComputerName',
        'IPAddress',
        'Lapor',
        'StatusBeli',
        'TanggalBeli',
        'Harga',
        'IDVendor',
        'Garansi',
        'DateWarranty',
        'IDPerusahaan',
        'NIK',
        'IDLokasi',
        'StatusAsset',
        'Keterangan',
    ];

    protected $casts = [
        'TanggalBeli' => 'datetime',
        'DateWarranty' => 'datetime',
        'Harga' => 'decimal:2',
        'Garansi' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if ($model->Garansi === null) {
                $model->Garansi = 0;
            }

        });

        static::updating(function ($model) {

            if ($model->Garansi === null) {
                $model->Garansi = 0;
            }

        });
    }

    public function lokasi()
    {
        return $this->belongsTo(
            MstLokasi::class,
            'IDLokasi',
            'IDLokasi'
        );
    }

    public function perusahaan()
    {
        return $this->belongsTo(
            MstPerusahaan::class,
            'IDPerusahaan'
        );
    }

    public function vendor()
    {
        return $this->belongsTo(
            MstVendor::class,
            'IDVendor'
        );
    }

    public function mutasiAsset()
    {
        return $this->hasMany(
            TrxMutasiAsset::class,
            'NoAssetIT',
            'NoAssetIT'
        );
    }
    public function karyawan()
    {
        return $this->belongsTo(
            MstKaryawan::class,
            'NIK',
            'NIK'
        );
    }

    public function service()
    {
        return $this->hasMany(
            TrxServiceAsset::class,
            'NoAssetIT',
            'NoAssetIT'
        );
    }



    public function retire()
    {
        return $this->hasMany(
            TrxRetireAsset::class,
            'NoAssetIT',
            'NoAssetIT'
        );
    }

    public function softwareAssignment()
    {
        return $this->hasMany(
            TrxSoftwareAssignment::class,
            'NoAssetIT',
            'NoAssetIT'
        );
    }



    // public function assignment()
    // {
    //     return $this->hasMany(
    //         TrxSoftwareAssignment::class,
    //         'IDLicense'
    //     );
    // }
}