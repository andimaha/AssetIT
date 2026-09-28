<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItRequestRequest;
use App\Models\ItRequest;
use App\Models\ItRequestApproval;
use App\Models\MstAsset;
use App\Models\MstJenisPermintaan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(): View
    {
        $user = auth()->user();

        $query = ItRequest::query()
            ->with([
                'pemohon.karyawan.departemen',
                'pemohon.karyawan.kepalaBagian.user',
                'penyelesai.karyawan',
                'jenisPermintaan',
                'assets',
                'relatedUsers.karyawan.departemen',
                'approval.approver.karyawan',

                /*
    |--------------------------------------------------------------------------
    | CATATAN USER TERKAIT
    |--------------------------------------------------------------------------
    */

    'relatedUserNotes.user',
            ]);

        if (!$user->hasAnyRole([
            'staff_it',
            'super_admin',
        ])) {

            $query->where(function ($query) use ($user) {

                /*
                |--------------------------------------------------------------------------
                | REQUEST SENDIRI
                |--------------------------------------------------------------------------
                */

                $query->where(
                    'UserPemohonID',
                    $user->id
                );

                /*
                |--------------------------------------------------------------------------
                | REQUEST DIMANA USER MENJADI RELATED USER
                |--------------------------------------------------------------------------
                */

                $query->orWhereHas(
                    'relatedUsers',
                    function ($query) use ($user) {

                        $query->where(
                            'users.id',
                            $user->id
                        );
                    }
                );
            });
        }

        $requests = $query
            ->latest('IDRequest')
            ->paginate(10)
            ->withQueryString();

        return view(
            'it_requests.index',
            compact('requests')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $jenisPermintaans = MstJenisPermintaan::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $assets = MstAsset::query()
            ->orderBy('NoAssetIT')
            ->get();

        $users = User::query()
            ->whereNotNull('NIK')
            ->with([
                'karyawan.departemen',
                'karyawan.kepalaBagian.user',
            ])
            ->orderBy('name')
            ->get();

        return view(
            'it_requests.create',
            compact(
                'jenisPermintaans',
                'assets',
                'users'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreItRequestRequest $request
    ): RedirectResponse {

        $validated = $request->validated();

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | AMBIL KEPALA BAGIAN
        |--------------------------------------------------------------------------
        |
        | Tidak lagi:
        |
        | $user->kepala_bagian_id
        |
        | Melainkan:
        |
        | users
        |   ↓
        | mstkaryawan
        |   ↓
        | NIKKepalaBagian
        |   ↓
        | mstkaryawan
        |   ↓
        | users
        |
        */

        $kepalaBagian = $user->kepalaBagian();

        if (!$kepalaBagian) {

            return back()
                ->withInput()
                ->withErrors([
                    'Permintaan' =>
                        'User Anda belum memiliki Kepala Bagian. ' .
                        'Silakan hubungi Super Admin sebelum mengajukan permintaan IT.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | PASTIKAN KEPALA BAGIAN MEMILIKI ROLE
        |--------------------------------------------------------------------------
        */

        if (!$kepalaBagian->hasRole('kepala_bagian')) {

            return back()
                ->withInput()
                ->withErrors([
                    'Permintaan' =>
                        'Kepala Bagian Anda belum memiliki role Kepala Bagian. ' .
                        'Silakan hubungi Super Admin.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $requestModel = DB::transaction(
            function () use (
                $validated,
                $user,
                $kepalaBagian
            ) {

                $requestModel = ItRequest::create([
                    'NoRequest' =>
                        'TEMP-' . uniqid(),

                    'UserPemohonID' =>
                        $user->id,

                    'Permintaan' =>
                        $validated['Permintaan'],

                    'Keterangan' =>
                        $validated['Keterangan'] ?? null,

                    'Status' =>
                        'diajukan',
                ]);

                /*
                |--------------------------------------------------------------------------
                | GENERATE NO REQUEST
                |--------------------------------------------------------------------------
                */

                $requestModel->update([
                    'NoRequest' =>
                        'IT-' .
                        now()->format('Ymd') .
                        '-' .
                        str_pad(
                            (string) $requestModel->IDRequest,
                            6,
                            '0',
                            STR_PAD_LEFT
                        ),
                ]);

                /*
                |--------------------------------------------------------------------------
                | JENIS PERMINTAAN
                |--------------------------------------------------------------------------
                */

                $requestModel
                    ->jenisPermintaan()
                    ->sync(
                        $validated['jenis_permintaan']
                    );

                /*
                |--------------------------------------------------------------------------
                | ASSET
                |--------------------------------------------------------------------------
                */

                $requestModel
                    ->assets()
                    ->sync(
                        $validated['assets'] ?? []
                    );

                /*
                |--------------------------------------------------------------------------
                | USER TERKAIT
                |--------------------------------------------------------------------------
                */

                $relatedUsers = collect(
                    $validated['related_users'] ?? []
                )
                    ->reject(
                        fn ($id) =>
                            (int) $id === (int) $user->id
                    )
                    ->values()
                    ->all();

                $requestModel
                    ->relatedUsers()
                    ->sync(
                        $relatedUsers
                    );

                /*
                |--------------------------------------------------------------------------
                | APPROVAL KEPALA BAGIAN
                |--------------------------------------------------------------------------
                |
                | Sekarang approval menyimpan users.id.
                |
                */

                ItRequestApproval::create([
                    'it_request_id' =>
                        $requestModel->IDRequest,

                    'approver_id' =>
                        $kepalaBagian->id,

                    'status' =>
                        ItRequestApproval::STATUS_PENDING,

                    'catatan' =>
                        null,

                    'approved_at' =>
                        null,
                ]);

                return $requestModel;
            }
        );

        return redirect()
            ->route(
                'it-requests.show',
                $requestModel
            )
            ->with(
                'success',
                'Permintaan IT berhasil diajukan dan menunggu persetujuan Kepala Bagian.'
            );
    }

    /*
|--------------------------------------------------------------------------
| SHOW
|--------------------------------------------------------------------------
*/

public function show(
    ItRequest $itRequest
): View {

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | CEK AKSES
    |--------------------------------------------------------------------------
    */

    $allowed =
        (int) $itRequest->UserPemohonID ===
        (int) $user->id

        ||

        $itRequest
            ->relatedUsers()
            ->where(
                'users.id',
                $user->id
            )
            ->exists();

    if (
        !$allowed
        &&
        !$user->hasAnyRole([
            'staff_it',
            'super_admin',
        ])
    ) {
        abort(403);
    }

    /*
    |--------------------------------------------------------------------------
    | CEK PEMOHON + STATUS UNTUK EDIT / DELETE
    |--------------------------------------------------------------------------
    */

    $isPemohon =
        (int) $itRequest->UserPemohonID ===
        (int) $user->id;

    $canModify =
        $isPemohon &&
        strtolower(
            (string) $itRequest->Status
        ) === 'diajukan';

    /*
    |--------------------------------------------------------------------------
    | CEK USER TERKAIT UNTUK CATATAN
    |--------------------------------------------------------------------------
    |
    | Hanya user yang terdaftar sebagai related user
    | yang boleh menambahkan catatan.
    |
    */

    $isRelatedUser =
        $itRequest
            ->relatedUsers()
            ->where(
                'users.id',
                $user->id
            )
            ->exists();

    $canAddRelatedUserNote =
        $isRelatedUser;

    /*
    |--------------------------------------------------------------------------
    | LOAD RELATIONS
    |--------------------------------------------------------------------------
    */

    $itRequest->load([
        'pemohon.karyawan.departemen',
        'pemohon.karyawan.kepalaBagian.user',
        'penyelesai.karyawan',
        'jenisPermintaan',
        'assets',
        'relatedUsers.karyawan.departemen',
        'approval.approver.karyawan',

        /*
        |--------------------------------------------------------------------------
        | CATATAN USER TERKAIT
        |--------------------------------------------------------------------------
        */

        'relatedUserNotes.user',
    ]);

    return view(
        'it_requests.show',
        compact(
            'itRequest',
            'canModify',
            'canAddRelatedUserNote'
        )
    );
}


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    |
    | Hanya PEMOHON asli yang boleh mengakses halaman edit.
    |
    | Syarat:
    |
    | 1. User login adalah pemohon.
    | 2. Status request harus "diajukan".
    |
    */

    public function edit(
        ItRequest $itRequest
    ): View {

        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | CEK PEMOHON
        |--------------------------------------------------------------------------
        */

        if (
            $itRequest->UserPemohonID !== $user->id
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK STATUS
        |--------------------------------------------------------------------------
        */

        if (
            strtolower(
                (string) $itRequest->Status
            ) !== 'diajukan'
        ) {
            abort(
                403,
                'Permintaan IT ini sudah tidak dapat diedit karena statusnya bukan lagi diajukan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DATA FORM
        |--------------------------------------------------------------------------
        */

        $jenisPermintaans = MstJenisPermintaan::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $assets = MstAsset::query()
            ->orderBy('NoAssetIT')
            ->get();

        $users = User::query()
            ->whereNotNull('NIK')
            ->where(
                'id',
                '!=',
                $user->id
            )
            ->with([
                'karyawan.departemen',
                'karyawan.kepalaBagian.user',
            ])
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | LOAD RELATION
        |--------------------------------------------------------------------------
        */

        $itRequest->load([
            'jenisPermintaan',
            'assets',
            'relatedUsers',
        ]);

        return view(
            'it_requests.edit',
            compact(
                'itRequest',
                'jenisPermintaans',
                'assets',
                'users'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        StoreItRequestRequest $request,
        ItRequest $itRequest
    ): RedirectResponse {

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | CEK PEMOHON
        |--------------------------------------------------------------------------
        */

        if (
            $itRequest->UserPemohonID !== $user->id
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK STATUS
        |--------------------------------------------------------------------------
        */

        if (
            strtolower(
                (string) $itRequest->Status
            ) !== 'diajukan'
        ) {

            return back()
                ->with(
                    'error',
                    'Permintaan IT hanya dapat diedit ketika status masih diajukan.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $validated,
                $itRequest,
                $user
            ) {

                /*
                |--------------------------------------------------------------------------
                | UPDATE DATA UTAMA
                |--------------------------------------------------------------------------
                */

                $itRequest->update([
                    'Permintaan' =>
                        $validated['Permintaan'],

                    'Keterangan' =>
                        $validated['Keterangan'] ?? null,

                    'UserPemohonID' =>
                        $user->id,

                    'Status' =>
                        'diajukan',
                ]);

                /*
                |--------------------------------------------------------------------------
                | JENIS PERMINTAAN
                |--------------------------------------------------------------------------
                */

                $itRequest
                    ->jenisPermintaan()
                    ->sync(
                        $validated['jenis_permintaan']
                    );

                /*
                |--------------------------------------------------------------------------
                | ASSET
                |--------------------------------------------------------------------------
                */

                $itRequest
                    ->assets()
                    ->sync(
                        $validated['assets'] ?? []
                    );

                /*
                |--------------------------------------------------------------------------
                | USER TERKAIT
                |--------------------------------------------------------------------------
                */

                $relatedUsers = collect(
                    $validated['related_users'] ?? []
                )
                    ->reject(
                        fn ($id) =>
                            (int) $id === (int) $user->id
                    )
                    ->values()
                    ->all();

                $itRequest
                    ->relatedUsers()
                    ->sync(
                        $relatedUsers
                    );
            }
        );

        return redirect()
            ->route(
                'it-requests.show',
                $itRequest
            )
            ->with(
                'success',
                'Permintaan IT berhasil diperbarui.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SERAH TERIMA
    |--------------------------------------------------------------------------
    */

    public function serahTerima(
        ItRequest $itRequest
    ): RedirectResponse {

        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | CEK PEMOHON
        |--------------------------------------------------------------------------
        */

        if (
            (int) $itRequest->UserPemohonID !==
            (int) $user->id
        ) {
            abort(
                403,
                'Anda tidak memiliki hak untuk melakukan konfirmasi serah terima pada permintaan ini.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CEK STATUS
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                strtolower((string) $itRequest->Status),
                [
                    'selesai',
                    'completed',
                    'done',
                ],
                true
            )
        ) {

            return back()
                ->with(
                    'error',
                    'Serah terima hanya dapat dilakukan setelah permintaan berstatus selesai.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | CEK SUDAH SERAH TERIMA
        |--------------------------------------------------------------------------
        */

        if ($itRequest->SerahTerima === true) {

            return back()
                ->with(
                    'error',
                    'Permintaan ini sudah dikonfirmasi serah terima sebelumnya.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN
        |--------------------------------------------------------------------------
        */

        $itRequest->update([
            'SerahTerima' =>
                true,

            'TanggalSerahTerima' =>
                now(),
        ]);

        return redirect()
            ->route(
                'it-requests.show',
                $itRequest
            )
            ->with(
                'success',
                'Serah terima berhasil dikonfirmasi. Terima kasih.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(
        ItRequest $itRequest
    ): RedirectResponse {

        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | CEK PEMOHON
        |--------------------------------------------------------------------------
        */

        if (
            $itRequest->UserPemohonID !== $user->id
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK STATUS
        |--------------------------------------------------------------------------
        */

        if (
            strtolower(
                (string) $itRequest->Status
            ) !== 'diajukan'
        ) {

            return back()
                ->with(
                    'error',
                    'Permintaan IT hanya dapat dihapus ketika status masih diajukan.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use ($itRequest) {

                /*
                |--------------------------------------------------------------------------
                | HAPUS RELASI PIVOT
                |--------------------------------------------------------------------------
                */

                $itRequest
                    ->jenisPermintaan()
                    ->detach();

                $itRequest
                    ->assets()
                    ->detach();

                $itRequest
                    ->relatedUsers()
                    ->detach();

                /*
                |--------------------------------------------------------------------------
                | HAPUS APPROVAL
                |--------------------------------------------------------------------------
                */

                ItRequestApproval::query()
                    ->where(
                        'it_request_id',
                        $itRequest->IDRequest
                    )
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | HAPUS REQUEST
                |--------------------------------------------------------------------------
                */

                $itRequest->delete();
            }
        );

        return redirect()
            ->route('it-requests.index')
            ->with(
                'success',
                'Permintaan IT berhasil dihapus.'
            );
    }
}
