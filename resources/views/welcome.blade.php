<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        IT Asset Management
    </title>


    <script src="https://cdn.tailwindcss.com"></script>


</head>


<body
    class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-amber-700 flex items-center justify-center"
>



<div
    class="bg-white/95 backdrop-blur-lg shadow-2xl rounded-3xl p-10 md:p-14 max-w-xl w-full text-center mx-5"
>


    {{-- LOGO --}}
    <div
        class="flex justify-center mb-8"
    >

        <div
            class="
                w-24
                h-24
                rounded-3xl
                bg-amber-500
                flex
                items-center
                justify-center
                shadow-lg
            "
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.8"
                stroke="white"
                class="w-14 h-14"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="
                    M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25Z
                    "
                />

            </svg>


        </div>


    </div>




    {{-- TITLE --}}
    <h1
        class="
            text-3xl
            md:text-4xl
            font-bold
            text-gray-800
            mb-4
        "
    >

        IT Asset Management

    </h1>




    <p
        class="
            text-gray-500
            text-base
            leading-relaxed
            mb-8
        "
    >

        Sistem manajemen aset IT untuk mengelola,
        memonitor, dan mendokumentasikan seluruh
        aset perusahaan secara terintegrasi.

    </p>




    {{-- BUTTON --}}
    <a
        href="/admin"
        class="
            inline-flex
            items-center
            justify-center
            gap-3
            bg-amber-500
            hover:bg-amber-600
            text-white
            font-semibold
            px-10
            py-4
            rounded-2xl
            shadow-lg
            transition
            duration-300
            hover:scale-105
        "
    >


        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            class="w-6 h-6"
        >

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="
                M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9
                "
            />

        </svg>


        Masuk Dashboard


    </a>




    <div
        class="
            mt-10
            text-sm
            text-gray-400
        "
    >

        © {{ date('Y') }} IT Asset Management

    </div>


</div>



</body>

</html>