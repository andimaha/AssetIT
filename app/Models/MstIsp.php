<?php

namespace App\Models;

use App\Models\Concerns\AuditLoggable;
use Illuminate\Database\Eloquent\Model;

class MstIsp extends Model
{
    use AuditLoggable;

    protected $table = 'mstisp';

    protected $primaryKey = 'IDISP';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'ISPCode',
        'ConnectionType',
        'NamaISP',
        'IDVendor',
        'IDPerusahaan',
        'IDLokasi',
        'MediaType',
        'ContractStart',
        'ContractEnd',
        'ContractPeriodMonth',
        'SLA',
        'Keterangan',
        'IsActive',
    ];

    protected $casts = [
        'IDISP' => 'integer',
        'IDVendor' => 'integer',
        'IDPerusahaan' => 'integer',
        'IDLokasi' => 'integer',
        'ContractStart' => 'date',
        'ContractEnd' => 'date',
        'ContractPeriodMonth' => 'integer',
        'SLA' => 'decimal:2',
        'IsActive' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke vendor.
     */
    public function vendor()
    {
        return $this->belongsTo(
            MstVendor::class,
            'IDVendor',
            'IDVendor'
        );
    }

    /**
     * Relasi ke perusahaan.
     */
    public function perusahaan()
    {
        return $this->belongsTo(
            MstPerusahaan::class,
            'IDPerusahaan',
            'IDPerusahaan'
        );
    }

    /**
     * Relasi ke lokasi.
     */
    public function lokasi()
    {
        return $this->belongsTo(
            MstLokasi::class,
            'IDLokasi',
            'IDLokasi'
        );
    }

    /**
     * Relasi ke histori bandwidth.
     */
    public function bandwidths()
    {
        return $this->hasMany(
            TrxIspBandwidth::class,
            'IDISP',
            'IDISP'
        );
    }

    /**
     * Relasi ke histori downtime.
     */
    public function downtimes()
    {
        return $this->hasMany(
            TrxIspDowntime::class,
            'IDISP',
            'IDISP'
        );
    }
}
