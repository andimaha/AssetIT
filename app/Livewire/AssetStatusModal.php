<?php

namespace App\Livewire;

use App\Models\MstAsset;
use Livewire\Component;
use Livewire\Attributes\On;

class AssetStatusModal extends Component
{
    public bool $show = false;

    public ?string $status = null;

    public ?string $company = 'all';

    #[On('open-asset-detail-modal')]
    public function open($status, $company = 'all')
    {
        $this->status = $status;
        $this->company = $company;
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
        $this->status = null;
        $this->company = 'all';
    }

    public function getAssetsProperty()
    {
        return MstAsset::query()
            ->when(
                $this->status !== 'all',
                fn ($query) => $query->where('StatusAsset', $this->status)
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
            ->get();
    }

    public function render()
    {
        return view('livewire.asset-status-modal');
    }
}