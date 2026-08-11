<?php

namespace App\Livewire;

use App\Models\MstAsset;
use App\Models\MstLokasi;
use Livewire\Attributes\On;
use Livewire\Component;

class AssetLocationStatusModal extends Component
{
    public bool $show = false;

    public ?string $status = null;

    public ?string $location = 'all';

    #[On('open-location-status-detail-modal')]
    public function open(
        $status,
        $location = 'all'
    ) {
        $this->status = $status;

        $this->location = $location;

        $this->show = true;
    }

    public function close()
    {
        $this->show = false;

        $this->status = null;

        $this->location = 'all';
    }

    public function getLocationNameProperty(): string
    {
        if (
            $this->location === null ||
            $this->location === 'all'
        ) {
            return 'Semua Lokasi';
        }

        return MstLokasi::query()
            ->where(
                'IDLokasi',
                $this->location
            )
            ->value('NamaLokasi') ?? '-';
    }

    public function getAssetsProperty()
    {
        return MstAsset::query()

            /*
             * FILTER STATUS
             */
            ->when(
                $this->status !== null &&
                $this->status !== 'all',
                fn ($query) =>
                    $query->where(
                        'StatusAsset',
                        $this->status
                    )
            )

            /*
             * FILTER LOKASI
             */
            ->when(
                $this->location !== null &&
                $this->location !== 'all',
                fn ($query) =>
                    $query->where(
                        'IDLokasi',
                        $this->location
                    )
            )

            /*
             * RELATIONSHIP
             */
            ->with([
                'perusahaan',
                'karyawan',
                'lokasi',
            ])

            ->orderBy(
                'NoAssetIT'
            )

            ->get();
    }

    public function render()
    {
        return view(
            'livewire.asset-location-status-modal'
        );
    }
}