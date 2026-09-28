<?php

namespace App\Livewire;

use App\Models\ItRequest;
use App\Models\MstJenisPermintaan;
use Carbon\Carbon;
use Livewire\Component;

class ItRequestDetailModal extends Component
{
    public bool $show = false;

    public ?string $bulan = null;

    public ?string $filter = null;

    /*
    |--------------------------------------------------------------------------
    | FILTER JENIS
    |--------------------------------------------------------------------------
    |
    | null / kosong = semua jenis
    |
    */

    public ?string $jenisFilter = null;


    /*
    |--------------------------------------------------------------------------
    | LISTENER
    |--------------------------------------------------------------------------
    */

    protected $listeners = [
        'open-it-request-detail-modal' => 'open',
    ];


    /*
    |--------------------------------------------------------------------------
    | OPEN
    |--------------------------------------------------------------------------
    */

    public function open($bulan, $filter): void
    {
        $this->bulan = $bulan;

        $this->filter = $filter;

        /*
        |--------------------------------------------------------------------------
        | RESET FILTER JENIS
        |--------------------------------------------------------------------------
        |
        | Setiap modal dibuka dari chart, filter jenis
        | dikembalikan ke semua jenis.
        |
        */

        $this->jenisFilter = null;

        $this->show = true;
    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE
    |--------------------------------------------------------------------------
    */

    public function close(): void
    {
        $this->show = false;

        $this->bulan = null;

        $this->filter = null;

        $this->jenisFilter = null;
    }


    /*
    |--------------------------------------------------------------------------
    | RESET FILTER JENIS
    |--------------------------------------------------------------------------
    */

    public function resetJenisFilter(): void
    {
        $this->jenisFilter = null;
    }


    /*
    |--------------------------------------------------------------------------
    | JENIS OPTIONS
    |--------------------------------------------------------------------------
    |
    | Ambil semua jenis aktif.
    |
    */

    public function getJenisOptionsProperty()
    {
        return MstJenisPermintaan::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | REQUEST DATA
    |--------------------------------------------------------------------------
    */

    public function getRequestsProperty()
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        if (blank($this->bulan)) {
            return collect();
        }


        /*
        |--------------------------------------------------------------------------
        | TENTUKAN TAHUN
        |--------------------------------------------------------------------------
        */

        $year = now()->year;


        if (filled($this->filter)) {

            /*
            |--------------------------------------------------------------------------
            | FILTER BULAN
            |--------------------------------------------------------------------------
            |
            | Contoh:
            | 2026-09
            |
            */

            if (
                preg_match(
                    '/^(\d{4})-(\d{2})$/',
                    $this->filter,
                    $matches
                )
            ) {

                $year = (int) $matches[1];
            }


            /*
            |--------------------------------------------------------------------------
            | FILTER TAHUN
            |--------------------------------------------------------------------------
            |
            | Contoh:
            | 2026
            |
            */

            elseif (
                preg_match(
                    '/^\d{4}$/',
                    $this->filter
                )
            ) {

                $year = (int) $this->filter;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CARI NOMOR BULAN
        |--------------------------------------------------------------------------
        */

        $monthNumber = null;


        for ($month = 1; $month <= 12; $month++) {

            $monthName = Carbon::create(
                $year,
                $month,
                1
            )->translatedFormat('F');


            if ($monthName === $this->bulan) {

                $monthNumber = $month;

                break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI BULAN
        |--------------------------------------------------------------------------
        */

        if (! $monthNumber) {
            return collect();
        }


        /*
        |--------------------------------------------------------------------------
        | QUERY
        |--------------------------------------------------------------------------
        */

        $query = ItRequest::query()
            ->with([
                'pemohon.karyawan.departemen',
                'jenisPermintaan',
                'penyelesai.karyawan',
            ])
            ->whereYear(
                'created_at',
                $year
            )
            ->whereMonth(
                'created_at',
                $monthNumber
            );


        /*
        |--------------------------------------------------------------------------
        | FILTER JENIS
        |--------------------------------------------------------------------------
        |
        | Karena satu request dapat memiliki banyak jenis,
        | gunakan whereHas().
        |
        */

        if (filled($this->jenisFilter)) {

            $query->whereHas(
                'jenisPermintaan',
                function ($jenisQuery) {

                    $jenisQuery->where(
                        'mstjenispermintaan.id',
                        $this->jenisFilter
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return $query
            ->orderBy(
                'created_at',
                'desc'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.it-request-detail-modal'
        );
    }
}
