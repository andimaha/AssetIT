<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\MstAsset;
use App\Models\MstPerusahaan;


class AssetCompanyModal extends Component
{

    public bool $show = false;



    public $company = null;



    public $status = 'all';







    protected $listeners = [

        'open-company-detail-modal' => 'open',

    ];









    public function open(
        $company,
        $status = 'all'
    )
    {

        $this->company = $company;


        $this->status = $status;


        $this->show = true;

    }









    public function close()
    {

        $this->show = false;

    }









    public function getCompanyDataProperty()
    {

        return MstPerusahaan::find(

            $this->company

        );

    }









    public function getAssetsProperty()
    {

        $query = MstAsset::query()

            ->with([

                'perusahaan',

                'karyawan',

                'lokasi',

            ]);







        $query->where(

            'IDPerusahaan',

            $this->company

        );








        if (

            $this->status !== null

            &&

            $this->status !== 'all'

        ) {

            $query->where(

                'StatusAsset',

                $this->status

            );

        }








        return $query

            ->orderBy(

                'NoAssetIT'

            )

            ->get();

    }









    public function render()
    {

        return view(

            'livewire.asset-company-modal'

        );

    }

}