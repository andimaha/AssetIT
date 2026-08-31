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
            width:95%;
            max-width:1300px;
            max-height:90vh;
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
                    #8B5CF6,
                    #6D28D9
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
                    Detail Software Assignment
                </div>

                <div
                    style="
                        margin-top:5px;
                        opacity:.9;
                    "
                >
                    Software :
                    <b>{{ $software }}</b>

                    |

                    Total Pemakai :
                    <b>{{ $this->total }}</b>
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
                max-height:68vh;
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
                            No
                        </th>

                        <th style="padding:12px;text-align:left">
                            Software
                        </th>

                        <th style="padding:12px;text-align:left">
                            Version
                        </th>

                        <th style="padding:12px;text-align:left">
                            NIK
                        </th>

                        <th style="padding:12px;text-align:left">
                            Nama Pemakai
                        </th>

                        <th style="padding:12px;text-align:left">
                            No Asset
                        </th>

                        <th style="padding:12px;text-align:left">
                            Nama Asset
                        </th>

                        <th style="padding:12px;text-align:left">
                            Computer Name
                        </th>

                        <th style="padding:12px;text-align:left">
                            Perusahaan
                        </th>

                        <th style="padding:12px;text-align:left">
                            Tanggal Assign
                        </th>

                        <th style="padding:12px;text-align:left">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse(
                    $this->assignments
                    as $index => $assignment
                )

                    <tr
                        style="
                            border-bottom:1px solid #e5e7eb;
                        "
                    >

                        <td style="padding:12px">
                            {{ $index + 1 }}
                        </td>

                        <td style="padding:12px">
                            {{ $assignment->NamaSoftware }}
                        </td>

                        <td style="padding:12px">
                            {{ $assignment->Version ?? '-' }}
                        </td>

                        <td style="padding:12px">
                            {{ $assignment->NIK ?? '-' }}
                        </td>

                        <td
                            style="
                                padding:12px;
                                font-weight:600;
                            "
                        >
                            {{ $assignment->NamaPemakai ?? 'Belum Ada Pemakai' }}
                        </td>

                        <td style="padding:12px">
                            {{ $assignment->NoAssetIT }}
                        </td>

                        <td style="padding:12px">
                            {{ $assignment->NamaAsset ?? '-' }}
                        </td>

                        <td style="padding:12px">
                            {{ $assignment->ComputerName ?? '-' }}
                        </td>

                        <td style="padding:12px">
                            {{ $assignment->NamaPerusahaan ?? '-' }}
                        </td>

                        <td style="padding:12px">
                            {{ $assignment->TanggalAssign
                                ? \Carbon\Carbon::parse(
                                    $assignment->TanggalAssign
                                )->format('d/m/Y H:i')
                                : '-'
                            }}
                        </td>

                        <td style="padding:12px">

                            <span
                                style="
                                    display:inline-block;
                                    padding:5px 10px;
                                    border-radius:999px;
                                    background:#DCFCE7;
                                    color:#166534;
                                    font-size:12px;
                                    font-weight:600;
                                "
                            >
                                {{ $assignment->StatusAssignment }}
                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="11"
                            style="
                                padding:30px;
                                text-align:center;
                                color:#6b7280;
                            "
                        >
                            Tidak ada data software assignment.
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
