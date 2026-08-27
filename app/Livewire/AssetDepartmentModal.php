<?php

namespace App\Livewire;

use App\Models\MstAsset;
use App\Models\MstKaryawan;
use Livewire\Component;
use Livewire\Attributes\On;

class AssetDepartmentModal extends Component
{
    public bool $show = false;

    public ?int $departmentId = null;

    public ?string $department = null;

    #[On('open-asset-department-modal')]
    public function open(
        $departmentId = null,
        $department = null
    ): void {
        $this->departmentId =
            $departmentId !== null
                ? (int) $departmentId
                : null;

        $this->department = $department;

        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;

        $this->departmentId = null;

        $this->department = null;
    }

    public function getAssetsProperty()
    {
        return MstAsset::query()

            ->when(
                $this->departmentId !== null,
                function ($query) {
                    $query->whereIn(
                        'NIK',
                        MstKaryawan::query()
                            ->where(
                                'IDDept',
                                $this->departmentId
                            )
                            ->select('NIK')
                    );
                }
            )

            // Department "Tidak Diketahui"
            // berarti asset yang tidak memiliki
            // karyawan / NIK yang tidak memiliki department.
            ->when(
                $this->departmentId === null,
                function ($query) {
                    $query->where(function ($query) {
                        $query
                            ->whereNull('NIK')
                            ->orWhereNotIn(
                                'NIK',
                                MstKaryawan::query()
                                    ->whereNotNull('IDDept')
                                    ->select('NIK')
                            );
                    });
                }
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
        return view(
            'livewire.asset-department-modal'
        );
    }
}
