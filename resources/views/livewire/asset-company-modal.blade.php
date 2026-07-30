<div>

@if($show)

<div
    style="
        position:fixed;
        inset:0;
        z-index:9999;
        display:flex;
        align-items:center;
        justify-content:center;
    "
>


    {{-- BACKDROP --}}
    <div
        wire:click="close"
        style="
            position:absolute;
            inset:0;
            background:rgba(0,0,0,.55);
            backdrop-filter:blur(4px);
        "
    ></div>




    {{-- MODAL BOX --}}
    <div
        style="
            position:relative;
            width:90%;
            max-width:1100px;
            max-height:85vh;
            background:white;
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 25px 50px rgba(0,0,0,.25);
        "
    >



        {{-- HEADER --}}
        <div
            style="
                background:linear-gradient(
                    135deg,
                    #3b82f6,
                    #2563eb
                );
                color:white;
                padding:20px 25px;
                display:flex;
                justify-content:space-between;
                align-items:center;
            "
        >

            <div>

                <div
                    style="
                        font-size:22px;
                        font-weight:700;
                    "
                >
                    Detail Asset
                </div>


                <div
                    style="
                        margin-top:5px;
                        opacity:.9;
                    "
                >

                    Perusahaan :
                    <b>{{ $this->company?->NamaPerusahaan ?? '-' }}</b>

                    |

                    Status :
                    <b>{{ $status == 'all' ? 'Semua Status' : $status }}</b>

                    |

                    Total :
                    <b>{{ $this->assets->count() }}</b>

                    Asset

                </div>


            </div>



            <button
                wire:click="close"
                style="
                    background:rgba(255,255,255,.2);
                    border:none;
                    color:white;
                    width:40px;
                    height:40px;
                    border-radius:50%;
                    font-size:22px;
                    cursor:pointer;
                "
            >
                ×
            </button>


        </div>






        {{-- CONTENT --}}
        <div
            style="
                padding:25px;
                overflow:auto;
                max-height:65vh;
            "
        >


            <table
                style="
                    width:100%;
                    border-collapse:collapse;
                "
            >

                <thead>


                <tr
                    style="
                        background:#f3f4f6;
                    "
                >

                    <th style="padding:12px;text-align:left">
                        No Asset
                    </th>


                    <th style="padding:12px;text-align:left">
                        Nama Asset
                    </th>


                    <th style="padding:12px;text-align:left">
                        Status
                    </th>


                    <th style="padding:12px;text-align:left">
                        Pemegang
                    </th>


                    <th style="padding:12px;text-align:left">
                        Lokasi
                    </th>


                </tr>


                </thead>




                <tbody>


                @forelse($this->assets as $asset)


                <tr
                    style="
                        border-bottom:1px solid #e5e7eb;
                    "
                >


                    <td style="padding:12px">
                        {{ $asset->NoAssetIT }}
                    </td>


                    <td style="padding:12px">
                        {{ $asset->Nama }}
                    </td>


                    <td style="padding:12px">
                        {{ $asset->StatusAsset }}
                    </td>


                    <td style="padding:12px">
                        {{ $asset->karyawan?->Nama ?? '-' }}
                    </td>


                    <td style="padding:12px">
                        {{ $asset->lokasi?->NamaLokasi ?? '-' }}
                    </td>


                </tr>


                @empty


                <tr>

                    <td
                        colspan="5"
                        style="
                            padding:30px;
                            text-align:center;
                            color:#6b7280;
                        "
                    >
                        Tidak ada data asset
                    </td>

                </tr>


                @endforelse


                </tbody>


            </table>


        </div>





        {{-- FOOTER --}}
        <div
            style="
                padding:15px 25px;
                background:#f9fafb;
                text-align:right;
            "
        >

            <button
                wire:click="close"
                style="
                    background:#374151;
                    color:white;
                    border:none;
                    padding:10px 20px;
                    border-radius:10px;
                    cursor:pointer;
                "
            >
                Tutup
            </button>


        </div>



    </div>


</div>


@endif


</div>