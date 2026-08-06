<?php

namespace App\Livewire;

use App\Models\MstAsset;
use Livewire\Component;
use Livewire\Attributes\On;

class AssetJenisCompanyModal extends Component
{
    public bool $show = false;

    public ?string $jenis = null;

    public ?string $company = 'all';

    #[On('open-asset-jenis-modal')]
    public function open($jenis, $company = 'all')
    {
        $this->jenis = $jenis;
        $this->company = $company;
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
        $this->jenis = null;
        $this->company = 'all';
    }

    public function getAssetsProperty()
    {
        return MstAsset::query()
            ->when(
                $this->jenis !== null,
                fn ($query) => $query->where('Jenis', $this->jenis)
            )
            ->when(
                $this->company !== 'all',
                fn ($query) => $query->where('IDPerusahaan', $this->company)
            )
            ->with([
                'perusahaan',
                'karyawan',
                'lokasi',
            ])
            ->orderBy('Nama')
            ->get();
    }

    public function render()
    {
        return view('livewire.asset-jenis-company-modal');
    }
}