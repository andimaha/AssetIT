<?php

namespace App\Models;

use App\Models\Concerns\AuditLoggable;
use Illuminate\Database\Eloquent\Model;

class MstPerusahaan extends Model
{
    use AuditLoggable;
    protected $table = 'mstperusahaan';

    protected $primaryKey = 'IDPerusahaan';

    public $timestamps = false;


    protected $fillable = [
        'NamaPerusahaan'
    ];



    public function assets()
    {
        return $this->hasMany(
            MstAsset::class,
            'IDPerusahaan'
        );
    }



    public function karyawan()
    {
        return $this->hasMany(
            MstKaryawan::class,
            'IDPerusahaan'
        );
    }



    public function softwareLicenses()
    {
        return $this->hasMany(
            MstSoftwareLicense::class,
            'IDPerusahaan'
        );
    }

    /**
 * Relasi ke ISP.
 */
public function isps()
{
    return $this->hasMany(
        MstIsp::class,
        'IDPerusahaan',
        'IDPerusahaan'
    );
}

}