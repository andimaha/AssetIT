<div>

    @if ($show)

        <div
            style="
                position: fixed;
                inset: 0;
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            "
        >

            {{-- BACKDROP --}}
            <div
                wire:click="close"
                style="
                    position: absolute;
                    inset: 0;
                    background: rgba(0, 0, 0, .55);
                    backdrop-filter: blur(4px);
                "
            ></div>


            {{-- MODAL BOX --}}
            <div
                style="
                    position: relative;
                    width: 90%;
                    max-width: 1200px;
                    max-height: 85vh;
                    background: white;
                    border-radius: 18px;
                    overflow: hidden;
                    box-shadow: 0 25px 50px rgba(0, 0, 0, .25);
                "
            >

                {{-- HEADER --}}
                <div
                    style="
                        background: linear-gradient(
                            135deg,
                            #f97316,
                            #ea580c
                        );
                        color: white;
                        padding: 20px 25px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    "
                >

                    <div>

                        <div
                            style="
                                font-size: 22px;
                                font-weight: 700;
                            "
                        >
                            Detail Service
                        </div>

                        <div
                            style="
                                margin-top: 5px;
                                opacity: .9;
                            "
                        >
                            Tahun :

                            <b>
                                {{ $tahun }}
                            </b>

                            |

                            Jumlah :

                            <b>
                                {{ $this->services->count() }}
                            </b>

                            Service
                        </div>

                    </div>


                    {{-- CLOSE BUTTON --}}
                    <button
                        type="button"
                        wire:click="close"
                        style="
                            background: rgba(255, 255, 255, .2);
                            border: none;
                            color: white;
                            width: 40px;
                            height: 40px;
                            border-radius: 50%;
                            font-size: 22px;
                            cursor: pointer;
                            line-height: 40px;
                        "
                    >
                        ×
                    </button>

                </div>


                {{-- CONTENT --}}
                <div
                    style="
                        padding: 25px;
                        overflow: auto;
                        max-height: 65vh;
                    "
                >

                    <table
                        style="
                            width: 100%;
                            border-collapse: collapse;
                        "
                    >

                        <thead>

                            <tr
                                style="
                                    background: #f3f4f6;
                                "
                            >

                                <th
                                    style="
                                        padding: 12px;
                                        text-align: left;
                                        white-space: nowrap;
                                    "
                                >
                                    No Asset
                                </th>

                                <th
                                    style="
                                        padding: 12px;
                                        text-align: left;
                                        white-space: nowrap;
                                    "
                                >
                                    Nama Asset
                                </th>

                                <th
                                    style="
                                        padding: 12px;
                                        text-align: left;
                                        white-space: nowrap;
                                    "
                                >
                                    Perusahaan
                                </th>

                                <th
                                    style="
                                        padding: 12px;
                                        text-align: left;
                                        white-space: nowrap;
                                    "
                                >
                                    Tanggal Masuk
                                </th>

                                <th
                                    style="
                                        padding: 12px;
                                        text-align: left;
                                        white-space: nowrap;
                                    "
                                >
                                    Jenis
                                </th>

                                <th
                                    style="
                                        padding: 12px;
                                        text-align: left;
                                        white-space: nowrap;
                                    "
                                >
                                    Tindakan
                                </th>

                                <th
                                    style="
                                        padding: 12px;
                                        text-align: left;
                                        white-space: nowrap;
                                    "
                                >
                                    Status
                                </th>

                                <th
                                    style="
                                        padding: 12px;
                                        text-align: left;
                                        white-space: nowrap;
                                    "
                                >
                                    Lama
                                </th>

                                <th
                                    style="
                                        padding: 12px;
                                        text-align: right;
                                        white-space: nowrap;
                                    "
                                >
                                    Biaya
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($this->services as $service)

                                <tr
                                    style="
                                        border-bottom: 1px solid #e5e7eb;
                                    "
                                >

                                    {{-- NO ASSET --}}
                                    <td
                                        style="
                                            padding: 12px;
                                            white-space: nowrap;
                                        "
                                    >
                                        {{ $service->asset?->NoAssetIT ?? '-' }}
                                    </td>


                                    {{-- NAMA ASSET --}}
                                    <td
                                        style="
                                            padding: 12px;
                                        "
                                    >
                                        {{ $service->asset?->Nama ?? '-' }}
                                    </td>


                                    {{-- PERUSAHAAN --}}
                                    <td
                                        style="
                                            padding: 12px;
                                            white-space: nowrap;
                                        "
                                    >
                                        {{ $service->asset?->perusahaan?->NamaPerusahaan ?? '-' }}
                                    </td>


                                    {{-- TANGGAL MASUK --}}
                                    <td
                                        style="
                                            padding: 12px;
                                            white-space: nowrap;
                                        "
                                    >
                                        {{ $service->TanggalMasuk?->format('d M Y') ?? '-' }}
                                    </td>


                                    {{-- JENIS SERVICE --}}
                                    <td
                                        style="
                                            padding: 12px;
                                        "
                                    >
                                        {{ $service->JenisService ?? '-' }}
                                    </td>


                                    {{-- TINDAKAN --}}
                                    <td
                                        style="
                                            padding: 12px;
                                            min-width: 200px;
                                        "
                                    >
                                        {{ $service->Tindakan ?? '-' }}
                                    </td>


                                    {{-- STATUS --}}
                                    <td
                                        style="
                                            padding: 12px;
                                            white-space: nowrap;
                                        "
                                    >
                                        {{ $service->StatusService ?? '-' }}
                                    </td>


                                    {{-- LAMA SERVICE --}}
                                    <td
                                        style="
                                            padding: 12px;
                                            white-space: nowrap;
                                        "
                                    >

                                        @if ($service->TanggalMasuk && $service->TanggalSelesai)

                                            {{ $service->TanggalMasuk->diffInDays($service->TanggalSelesai) }}
                                            Hari

                                        @else

                                            -

                                        @endif

                                    </td>


                                    {{-- BIAYA --}}
                                    <td
                                        style="
                                            padding: 12px;
                                            text-align: right;
                                            white-space: nowrap;
                                        "
                                    >
                                        Rp
                                        {{ number_format(
                                            $service->Biaya ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="9"
                                        style="
                                            padding: 30px;
                                            text-align: center;
                                            color: #6b7280;
                                        "
                                    >
                                        Tidak ada data service
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- FOOTER --}}
                <div
                    style="
                        padding: 15px 25px;
                        background: #f9fafb;
                        text-align: right;
                    "
                >

                    <button
                        type="button"
                        wire:click="close"
                        style="
                            background: #374151;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 10px;
                            cursor: pointer;
                        "
                    >
                        Tutup
                    </button>

                </div>

            </div>

        </div>

    @endif

</div>
