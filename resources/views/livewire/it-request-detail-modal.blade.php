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


    {{-- MODAL --}}

    <div
        style="
            position:relative;
            width:94%;
            max-width:1400px;
            max-height:90vh;
            background:white;
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 25px 50px rgba(0,0,0,.25);
        "
        class="dark:bg-gray-900"
    >

        {{-- HEADER --}}

        <div
            style="
                background:linear-gradient(
                    135deg,
                    #8b5cf6,
                    #6d28d9
                );
                color:white;
                padding:20px 25px;
                display:flex;
                justify-content:space-between;
                align-items:center;
                gap:20px;
            "
        >

            <div>

                <div
                    style="
                        font-size:21px;
                        font-weight:700;
                    "
                >
                    Detail Permintaan IT
                </div>

                <div
                    style="
                        margin-top:6px;
                        opacity:.9;
                        font-size:13px;
                    "
                >

                    Bulan:
                    <b>{{ $bulan ?? '-' }}</b>

                    &nbsp; | &nbsp;

                    Total:
                    <b>{{ $this->requests->count() }}</b>

                    Request

                </div>

            </div>


            <button
                type="button"
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
                    flex-shrink:0;
                "
            >
                ×
            </button>

        </div>


        {{-- FILTER --}}

        <div
            style="
                padding:18px 25px;
                background:#fafafa;
                border-bottom:1px solid #e5e7eb;
            "
        >

            <div
                style="
                    display:flex;
                    align-items:end;
                    gap:15px;
                    flex-wrap:wrap;
                "
            >

                {{-- FILTER JENIS --}}

                <div
                    style="
                        min-width:280px;
                        max-width:420px;
                    "
                >

                    <label
                        for="jenisFilter"
                        style="
                            display:block;
                            margin-bottom:7px;
                            font-size:13px;
                            font-weight:600;
                            color:#374151;
                        "
                    >
                        Filter Jenis
                    </label>


                    <select
                        id="jenisFilter"
                        wire:model.live="jenisFilter"
                        style="
                            width:100%;
                            padding:10px 12px;
                            border:1px solid #d1d5db;
                            border-radius:10px;
                            background:white;
                            color:#111827;
                            outline:none;
                        "
                    >

                        <option value="">
                            Semua Jenis
                        </option>


                        @foreach($this->jenisOptions as $jenis)

                            <option
                                value="{{ $jenis->id }}"
                            >
                                {{ $jenis->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- RESET FILTER --}}

                @if(filled($jenisFilter))

                    <button
                        type="button"
                        wire:click="resetJenisFilter"
                        style="
                            background:#6b7280;
                            color:white;
                            border:none;
                            padding:10px 16px;
                            border-radius:10px;
                            cursor:pointer;
                            height:40px;
                        "
                    >
                        Reset Filter
                    </button>

                @endif

            </div>

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
                    min-width:1050px;
                "
            >

                <thead>

                    <tr
                        style="
                            background:#f3f4f6;
                        "
                    >

                        <th
                            style="
                                padding:12px;
                                text-align:left;
                                white-space:nowrap;
                            "
                        >
                            No.
                        </th>


                        <th
                            style="
                                padding:12px;
                                text-align:left;
                                white-space:nowrap;
                            "
                        >
                            No. Request
                        </th>


                        <th
                            style="
                                padding:12px;
                                text-align:left;
                            "
                        >
                            Pemohon
                        </th>


                        <th
                            style="
                                padding:12px;
                                text-align:left;
                            "
                        >
                            Departemen
                        </th>


                        {{-- JENIS --}}

                        <th
                            style="
                                padding:12px;
                                text-align:left;
                                min-width:180px;
                            "
                        >
                            Jenis
                        </th>


                        <th
                            style="
                                padding:12px;
                                text-align:left;
                                min-width:250px;
                            "
                        >
                            Permintaan
                        </th>


                        <th
                            style="
                                padding:12px;
                                text-align:left;
                            "
                        >
                            Status
                        </th>


                        <th
                            style="
                                padding:12px;
                                text-align:left;
                                white-space:nowrap;
                            "
                        >
                            Tanggal
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(
                        $this->requests
                        as $index => $request
                    )

                        <tr
                            style="
                                border-bottom:1px solid #e5e7eb;
                            "
                        >

                            {{-- NO --}}

                            <td
                                style="
                                    padding:12px;
                                "
                            >
                                {{ $index + 1 }}
                            </td>


                            {{-- NO REQUEST --}}

                            <td
                                style="
                                    padding:12px;
                                    font-weight:600;
                                    white-space:nowrap;
                                "
                            >
                                {{ $request->NoRequest ?? '-' }}
                            </td>


                            {{-- PEMOHON --}}

                            <td
                                style="
                                    padding:12px;
                                "
                            >

                                {{
                                    $request
                                        ->pemohon
                                        ?->karyawan
                                        ?->Nama
                                    ??
                                    $request
                                        ->pemohon
                                        ?->name
                                    ??
                                    '-'
                                }}

                            </td>


                            {{-- DEPARTEMEN --}}

                            <td
                                style="
                                    padding:12px;
                                "
                            >

                                {{
                                    $request
                                        ->pemohon
                                        ?->karyawan
                                        ?->departemen
                                        ?->NamaDept
                                    ??
                                    '-'
                                }}

                            </td>


                            {{-- JENIS --}}

                            <td
                                style="
                                    padding:12px;
                                    vertical-align:top;
                                "
                            >

                                @if(
                                    $request
                                        ->jenisPermintaan
                                        ->isNotEmpty()
                                )

                                    <div
                                        style="
                                            display:flex;
                                            flex-wrap:wrap;
                                            gap:6px;
                                        "
                                    >

                                        @foreach(
                                            $request->jenisPermintaan
                                            as $jenis
                                        )

                                            <span
                                                style="
                                                    display:inline-block;
                                                    background:#ede9fe;
                                                    color:#6d28d9;
                                                    padding:5px 9px;
                                                    border-radius:999px;
                                                    font-size:12px;
                                                    font-weight:600;
                                                    white-space:nowrap;
                                                "
                                            >
                                                {{ $jenis->name }}
                                            </span>

                                        @endforeach

                                    </div>

                                @else

                                    <span
                                        style="
                                            color:#9ca3af;
                                        "
                                    >
                                        -

                                    </span>

                                @endif

                            </td>


                            {{-- PERMINTAAN --}}

                            <td
                                style="
                                    padding:12px;
                                    min-width:250px;
                                "
                            >

                                {{ $request->Permintaan ?? '-' }}

                            </td>


                            {{-- STATUS --}}

                            <td
                                style="
                                    padding:12px;
                                    white-space:nowrap;
                                "
                            >

                                @php

                                    $status =
                                        $request->Status;

                                    $label = match ($status) {

                                        'diajukan' =>
                                            'Diajukan',

                                        'disetujui' =>
                                            'Disetujui',

                                        'diproses' =>
                                            'Diproses',

                                        'selesai' =>
                                            'Selesai',

                                        'ditolak' =>
                                            'Ditolak',

                                        'dibatalkan' =>
                                            'Dibatalkan',

                                        default =>
                                            $status
                                            ?
                                            ucfirst($status)
                                            :
                                            '-',

                                    };

                                @endphp

                                {{ $label }}

                            </td>


                            {{-- TANGGAL --}}

                            <td
                                style="
                                    padding:12px;
                                    white-space:nowrap;
                                "
                            >

                                {{
                                    $request->created_at
                                        ?->format(
                                            'd/m/Y H:i'
                                        )
                                    ??
                                    '-'
                                }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                style="
                                    padding:40px;
                                    text-align:center;
                                    color:#6b7280;
                                "
                            >

                                @if(filled($jenisFilter))

                                    Tidak ada data permintaan IT
                                    untuk jenis yang dipilih.

                                @else

                                    Tidak ada data permintaan IT.

                                @endif

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
                type="button"
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
