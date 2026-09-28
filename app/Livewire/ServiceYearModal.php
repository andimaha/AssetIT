<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Models\TrxServiceAsset;

class ServiceYearModal extends Component
{
    public bool $show = false;

    public ?string $tahun = null;

    public ?string $company = 'all';

    public ?string $jenis = null;


    #[On('open-service-year-modal')]
    public function open(
        $tahun,
        $company = 'all',
        $jenis = null
    ) {

        $this->tahun = $tahun;

        $this->company = $company;

        $this->jenis = $jenis;

        $this->show = true;
    }


    public function close()
    {
        $this->show = false;

        $this->tahun = null;

        $this->jenis = null;
    }


    public function getServicesProperty()
    {
        return TrxServiceAsset::query()

            ->whereYear(
                'TanggalMasuk',
                $this->tahun
            )

            ->when(
                $this->company !== 'all',
                function ($q) {

                    $q->whereHas(
                        'asset',
                        function ($a) {

                            $a->where(
                                'IDPerusahaan',
                                $this->company
                            );

                        }
                    );

                }
            )

            ->when(
                $this->jenis !== null,
                function ($q) {

                    if ($this->jenis === 'Tidak Ada Jenis') {

                        $q->whereNull('JenisService');

                    } else {

                        $q->where(
                            'JenisService',
                            $this->jenis
                        );

                    }

                }
            )

            ->with([

                'asset.perusahaan',

                'vendor'

            ])

            ->orderBy(
                'TanggalMasuk',
                'desc'
            )

            ->get();
    }


    public function render()
    {
        return view(
            'livewire.service-year-modal'
        );
    }
}
