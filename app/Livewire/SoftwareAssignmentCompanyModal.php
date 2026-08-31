<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class SoftwareAssignmentCompanyModal extends Component
{
    public bool $show = false;

    public ?string $software = null;

    public ?string $company = 'all';

    #[On('open-software-assignment-modal')]
    public function open($software, $company = 'all')
    {
        $this->software = $software;
        $this->company = $company;
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
        $this->software = null;
        $this->company = 'all';
    }

    public function getAssignmentsProperty()
    {
        $query = DB::table('trxsoftwareassignment as tsa')
            ->join(
                'mstsoftwarelicense as msl',
                'tsa.IDLicense',
                '=',
                'msl.IDLicense'
            )
            ->join(
                'mstsoftware as ms',
                'msl.IDSoftware',
                '=',
                'ms.IDSoftware'
            )
            ->join(
                'mstasset as ma',
                'tsa.NoAssetIT',
                '=',
                'ma.NoAssetIT'
            )
            ->leftJoin(
                'mstkaryawan as mk',
                'ma.NIK',
                '=',
                'mk.NIK'
            )
            ->leftJoin(
                'mstperusahaan as mp',
                'ma.IDPerusahaan',
                '=',
                'mp.IDPerusahaan'
            )
            ->where(
                'ms.NamaSoftware',
                $this->software
            )
            ->whereNull(
                'tsa.TanggalRevoke'
            )
            ->where(
                'tsa.StatusAssignment',
                'Installed'
            );

        if ($this->company !== 'all') {
            $query->where(
                'ma.IDPerusahaan',
                $this->company
            );
        }

        return $query
            ->select([
                'tsa.IDAssignment',
                'tsa.IDLicense',
                'tsa.TanggalAssign',
                'tsa.StatusAssignment',

                'ms.NamaSoftware',
                'ms.Version',

                'ma.NoAssetIT',
                'ma.Nama as NamaAsset',
                'ma.ComputerName',

                'mk.NIK',
                'mk.Nama as NamaPemakai',

                'mp.NamaPerusahaan',
            ])
            ->orderBy('mk.Nama')
            ->get();
    }

    public function getTotalProperty()
    {
        return $this->assignments->count();
    }

    public function render()
    {
        return view(
            'livewire.software-assignment-company-modal'
        );
    }
}
