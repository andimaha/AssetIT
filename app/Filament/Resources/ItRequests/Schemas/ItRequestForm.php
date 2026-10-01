<?php

namespace App\Filament\Resources\ItRequests\Schemas;

use App\Models\MstAsset;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ItRequestForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        /*
        |--------------------------------------------------------------------------
        | CEK SUPER ADMIN
        |--------------------------------------------------------------------------
        |
        | HANYA super_admin yang boleh bypass lock.
        |
        | Permission:
        |
        | - itrequest.view
        | - itrequest.create
        | - itrequest.update
        | - permission lainnya
        |
        | TIDAK membuat user bebas dari business-rule lock.
        |
        */

        $isSuperAdmin = function (): bool {

            return
                auth()->check()
                &&
                auth()->user()->hasRole(
                    'super_admin'
                );
        };

        /*
        |--------------------------------------------------------------------------
        | CEK REQUEST SUDAH SELESAI
        |--------------------------------------------------------------------------
        |
        | Jika sudah selesai:
        |
        | - User biasa       => LOCK
        | - Staff IT         => LOCK
        | - Kepala Bagian   => LOCK
        | - User permission  => LOCK
        | - super_admin      => BYPASS
        |
        */

        $isCompletedAndLocked = function ($record) use ($isSuperAdmin): bool {

            if (!$record) {
                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | HANYA SUPER ADMIN BOLEH BYPASS
            |--------------------------------------------------------------------------
            */

            if ($isSuperAdmin()) {
                return false;
            }

            return
                $record->Status === 'selesai';
        };

        /*
        |--------------------------------------------------------------------------
        | CEK APPROVAL DITOLAK
        |--------------------------------------------------------------------------
        |
        | Jika approval sudah rejected:
        |
        | - User biasa       => LOCK
        | - Staff IT         => LOCK
        | - Kepala Bagian   => LOCK
        | - User permission  => LOCK
        | - super_admin      => BYPASS
        |
        */

        $isRejectedAndLocked = function ($record) use ($isSuperAdmin): bool {

            if (!$record) {
                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | HANYA SUPER ADMIN BOLEH BYPASS
            |--------------------------------------------------------------------------
            */

            if ($isSuperAdmin()) {
                return false;
            }

            return
                $record
                    ->approval
                    ?->status === 'rejected';
        };

        /*
        |--------------------------------------------------------------------------
        | CEK REQUEST SUDAH TERKUNCI
        |--------------------------------------------------------------------------
        |
        | Request terkunci jika:
        |
        | 1. Sudah selesai
        | ATAU
        | 2. Approval ditolak
        |
        | Permission TIDAK mempengaruhi lock.
        |
        */

        $isRequestLocked = function ($record) use (
            $isCompletedAndLocked,
            $isRejectedAndLocked
        ): bool {

            return
                $isCompletedAndLocked($record)
                ||
                $isRejectedAndLocked($record);
        };

        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | INFORMASI REQUEST
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Informasi Permintaan'
                )
                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | NO REQUEST
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make(
                            'NoRequest'
                        )
                            ->label(
                                'No. Request'
                            )
                            ->disabled()
                            ->dehydrated(false),

                        /*
                        |--------------------------------------------------------------------------
                        | PEMOHON
                        |--------------------------------------------------------------------------
                        */

                        Select::make(
                            'UserPemohonID'
                        )
                            ->label(
                                'Pemohon'
                            )
                            ->relationship(
                                'pemohon',
                                'name'
                            )
                            ->getOptionLabelFromRecordUsing(
                                function (User $record): string {

                                    return
                                        ($record->NIK ?? '-')
                                        . ' | '
                                        . (
                                            $record->karyawan?->Nama
                                            ?? $record->name
                                        );
                                }
                            )
                            ->searchable([
                                'name',
                                'email',
                                'NIK',
                            ])
                            ->preload()
                            ->disabled()
                            ->dehydrated(false),

                        /*
                        |--------------------------------------------------------------------------
                        | JENIS PERMINTAAN
                        |--------------------------------------------------------------------------
                        */

                        Select::make(
                            'jenisPermintaan'
                        )
                            ->label(
                                'Jenis Permintaan'
                            )
                            ->relationship(
                                'jenisPermintaan',
                                'name'
                            )
                            ->getOptionLabelFromRecordUsing(
                                function ($record): string {

                                    return
                                        $record->Nama
                                        ?? $record->name
                                        ?? $record->JenisPermintaan
                                        ?? '-';
                                }
                            )
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        /*
                        |--------------------------------------------------------------------------
                        | ASSET IT
                        |--------------------------------------------------------------------------
                        */

                        Select::make(
                            'assets'
                        )
                            ->label(
                                'No. Asset IT'
                            )
                            ->relationship(
                                'assets',
                                'NoAssetIT',
                                modifyQueryUsing: function (Builder $query) {

                                    $query->with([
                                        'karyawan.departemen',
                                    ]);
                                }
                            )
                            ->getOptionLabelFromRecordUsing(
                                function ($record): string {

                                    $dept =
                                        $record
                                            ->karyawan
                                            ?->departemen
                                            ?->NamaDept
                                        ?? $record
                                            ->karyawan
                                            ?->departemen
                                            ?->NamaDepartemen
                                        ?? '-';

                                    return
                                        ($record->NoAssetIT ?? '-')
                                        . ' | '
                                        . ($record->Nama ?? '-')
                                        . ' | '
                                        . (
                                            $record
                                                ->karyawan
                                                ?->Nama
                                            ?? '-'
                                        )
                                        . ' | '
                                        . $dept;
                                }
                            )
                            ->searchable()
                            ->getSearchResultsUsing(
                                function (string $search): array {

                                    return MstAsset::query()
                                        ->with([
                                            'karyawan.departemen',
                                        ])
                                        ->where(
                                            function (Builder $query) use ($search) {

                                                $query
                                                    ->where(
                                                        'mstasset.NoAssetIT',
                                                        'like',
                                                        "%{$search}%"
                                                    )
                                                    ->orWhere(
                                                        'mstasset.Nama',
                                                        'like',
                                                        "%{$search}%"
                                                    )
                                                    ->orWhere(
                                                        'mstasset.NIK',
                                                        'like',
                                                        "%{$search}%"
                                                    )
                                                    ->orWhereHas(
                                                        'karyawan',
                                                        function (Builder $query) use ($search) {

                                                            $query
                                                                ->where(
                                                                    'mstkaryawan.Nama',
                                                                    'like',
                                                                    "%{$search}%"
                                                                )
                                                                ->orWhereHas(
                                                                    'departemen',
                                                                    function (Builder $query) use ($search) {

                                                                        /*
                                                                        |--------------------------------------------------------------------------
                                                                        | SEARCH DEPARTEMEN
                                                                        |--------------------------------------------------------------------------
                                                                        */

                                                                        $query->where(
                                                                            'mstdepartemen.NamaDept',
                                                                            'like',
                                                                            "%{$search}%"
                                                                        );
                                                                    }
                                                                );
                                                        }
                                                    );
                                            }
                                        )
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(
                                            function ($asset) {

                                                $dept =
                                                    $asset
                                                        ->karyawan
                                                        ?->departemen
                                                        ?->NamaDept
                                                    ?? $asset
                                                        ->karyawan
                                                        ?->departemen
                                                        ?->NamaDepartemen
                                                    ?? '-';

                                                return [
                                                    $asset->NoAssetIT =>
                                                        ($asset->NoAssetIT ?? '-')
                                                        . ' | '
                                                        . ($asset->Nama ?? '-')
                                                        . ' | '
                                                        . (
                                                            $asset
                                                                ->karyawan
                                                                ?->Nama
                                                            ?? '-'
                                                        )
                                                        . ' | '
                                                        . $dept,
                                                ];
                                            }
                                        )
                                        ->toArray();
                                }
                            )
                            ->preload()
                            ->multiple()
                            ->nullable()
                            ->disabled(
                                $isRequestLocked
                            )
                            ->helperText(
                                'Asset dapat dipilih lebih dari satu.'
                            )
                            ->columnSpanFull(),

                        /*
                        |--------------------------------------------------------------------------
                        | PERMINTAAN
                        |--------------------------------------------------------------------------
                        */

                        Textarea::make(
                            'Permintaan'
                        )
                            ->label(
                                'Permintaan'
                            )
                            ->rows(6)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        /*
                        |--------------------------------------------------------------------------
                        | KETERANGAN
                        |--------------------------------------------------------------------------
                        */

                        Textarea::make(
                            'Keterangan'
                        )
                            ->label(
                                'Keterangan'
                            )
                            ->rows(8)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        /*
                        |--------------------------------------------------------------------------
                        | BAGIAN TERKAIT
                        |--------------------------------------------------------------------------
                        */

                        Select::make(
                            'relatedUsers'
                        )
                            ->label(
                                'Bagian Terkait'
                            )
                            ->relationship(
                                'relatedUsers',
                                'name'
                            )
                            ->getOptionLabelFromRecordUsing(
                                function (User $record): string {

                                    $nik =
                                        $record->NIK
                                        ?? '-';

                                    $nama =
                                        $record->karyawan?->Nama
                                        ?? $record->name;

                                    $dept =
                                        $record
                                            ->karyawan
                                            ?->departemen
                                            ?->NamaDept
                                        ?? $record
                                            ->karyawan
                                            ?->departemen
                                            ?->NamaDepartemen
                                        ?? '-';

                                    return
                                        "{$nik} | {$nama} | {$dept}";
                                }
                            )
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->helperText(
                                'Bagian terkait tidak dapat diubah dari halaman Admin.'
                            ),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | APPROVAL KEPALA BAGIAN
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Persetujuan Kepala Bagian'
                )
                    ->schema([

                        TextInput::make(
                            'approval_kepala_bagian'
                        )
                            ->label(
                                'Kepala Bagian'
                            )
                            ->formatStateUsing(
                                function ($state, $record): string {

                                    $approver =
                                        $record
                                            ?->approval
                                            ?->approver;

                                    if (!$approver) {
                                        return '-';
                                    }

                                    $nik =
                                        $approver->NIK
                                        ?? '-';

                                    $nama =
                                        $approver
                                            ->karyawan
                                            ?->Nama
                                        ?? $approver->name
                                        ?? '-';

                                    return
                                        "{$nik} | {$nama}";
                                }
                            )
                            ->disabled()
                            ->dehydrated(false),

                        /*
                        |--------------------------------------------------------------------------
                        | STATUS APPROVAL
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make(
                            'approval_status'
                        )
                            ->label(
                                'Status Approval'
                            )
                            ->formatStateUsing(
                                function ($state, $record): string {

                                    return match (
                                        $record
                                            ?->approval
                                            ?->status
                                    ) {

                                        'pending' =>
                                            'Menunggu Persetujuan',

                                        'approved' =>
                                            'Disetujui',

                                        'rejected' =>
                                            'Ditolak',

                                        default =>
                                            'Belum Ada',
                                    };
                                }
                            )
                            ->disabled()
                            ->dehydrated(false),

                        /*
                        |--------------------------------------------------------------------------
                        | TANGGAL APPROVAL
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make(
                            'approval_date'
                        )
                            ->label(
                                'Tanggal Persetujuan'
                            )
                            ->formatStateUsing(
                                function ($state, $record): string {

                                    return
                                        $record
                                            ?->approval
                                            ?->approved_at
                                            ?->format(
                                                'd/m/Y H:i'
                                            )
                                        ?? '-';
                                }
                            )
                            ->disabled()
                            ->dehydrated(false),

                        /*
                        |--------------------------------------------------------------------------
                        | CATATAN KEPALA BAGIAN
                        |--------------------------------------------------------------------------
                        */

                        Textarea::make(
                            'approval_catatan'
                        )
                            ->label(
                                'Catatan Kepala Bagian'
                            )
                            ->formatStateUsing(
                                function ($state, $record): string {

                                    return
                                        $record
                                            ?->approval
                                            ?->catatan
                                        ?? '-';
                                }
                            )
                            ->rows(5)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | PENYELESAIAN
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Penyelesaian'
                )
                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | PENYELESAI
                        |--------------------------------------------------------------------------
                        |
                        | PENTING:
                        |
                        | Jangan menggunakan relationship() untuk field ini.
                        |
                        | Field ini hanya digunakan untuk MENAMPILKAN siapa
                        | yang menyelesaikan request sekaligus menyimpan ID.
                        |
                        | Dengan options() kita memastikan Filament tidak
                        | menampilkan ID mentah seperti:
                        |
                        |     8
                        |
                        | tetapi menampilkan:
                        |
                        |     Andi Mahawijaya - 4124021421
                        |
                        */

                        Select::make(
                            'UserPenyelesaiID'
                        )
                            ->label(
                                'Yang Menyelesaikan'
                            )

                            ->options(
                                function (): array {

                                    return User::query()
                                        ->with([
                                            'karyawan',
                                        ])
                                        ->whereHas(
                                            'permissions',
                                            function (Builder $query) {

                                                $query
                                                    ->where(
                                                        'name',
                                                        'itrequest.update'
                                                    )
                                                    ->where(
                                                        'guard_name',
                                                        'web'
                                                    );
                                            }
                                        )
                                        ->get()
                                        ->mapWithKeys(
                                            function (User $user): array {

                                                $nama =
                                                    $user
                                                        ->karyawan
                                                        ?->Nama
                                                    ?? $user->name
                                                    ?? '-';

                                                $nik =
                                                    $user->NIK
                                                    ?? '-';

                                                return [
                                                    (string) $user->getKey() =>
                                                        "{$nama} - {$nik}",
                                                ];
                                            }
                                        )
                                        ->toArray();
                                }
                            )

                            /*
                            |--------------------------------------------------------------------------
                            | LABEL FALLBACK
                            |--------------------------------------------------------------------------
                            |
                            | Jika ID yang tersimpan ternyata tidak lagi masuk
                            | query options, tetap ambil user berdasarkan ID
                            | agar tidak tampil sebagai angka mentah.
                            |
                            */

                            ->getOptionLabelUsing(
                                function ($value): ?string {

                                    if (
                                        blank($value)
                                    ) {
                                        return null;
                                    }

                                    $user =
                                        User::query()
                                            ->with([
                                                'karyawan',
                                            ])
                                            ->find($value);

                                    if (!$user) {
                                        return null;
                                    }

                                    $nama =
                                        $user
                                            ->karyawan
                                            ?->Nama
                                        ?? $user->name
                                        ?? '-';

                                    $nik =
                                        $user->NIK
                                        ?? '-';

                                    return
                                        "{$nama} - {$nik}";
                                }
                            )

                            ->searchable()

                            ->preload()

                            ->default(
                                fn (): ?int =>
                                    auth()->id()
                            )

                            ->afterStateHydrated(
                                function (
                                    Select $component,
                                    $state
                                ): void {

                                    /*
                                    |--------------------------------------------------------------------------
                                    | JIKA BELUM ADA PENYELESAI
                                    |--------------------------------------------------------------------------
                                    */

                                    if (
                                        blank($state)
                                        &&
                                        auth()->check()
                                    ) {

                                        $component->state(
                                            auth()->id()
                                        );
                                    }
                                }
                            )

                            /*
                            |--------------------------------------------------------------------------
                            | TETAP TIDAK DAPAT DIUBAH DARI FORM
                            |--------------------------------------------------------------------------
                            */

                            ->disabled()

                            /*
                            |--------------------------------------------------------------------------
                            | VALUE TETAP DIKIRIM SAAT SAVE
                            |--------------------------------------------------------------------------
                            */

                            ->dehydrated(),

                        /*
                        |--------------------------------------------------------------------------
                        | STATUS
                        |--------------------------------------------------------------------------
                        */

                        Select::make(
                            'Status'
                        )
                            ->label(
                                'Status'
                            )

                            ->options(
                                function ($record): array {

                                    if (
                                        $record
                                        && $record
                                            ->approval
                                            ?->status === 'approved'
                                    ) {

                                        $options = [
                                            'diproses' =>
                                                'Diproses',

                                            'selesai' =>
                                                'Selesai',

                                            'dibatalkan' =>
                                                'Dibatalkan',
                                        ];

                                        if (
                                            $record->Status === 'diajukan'
                                        ) {

                                            $options = [
                                                'diajukan' =>
                                                    'Diajukan',

                                                ...$options,
                                            ];
                                        }

                                        if (
                                            in_array(
                                                $record->Status,
                                                [
                                                    'disetujui',
                                                    'ditolak',
                                                ],
                                                true
                                            )
                                        ) {

                                            $options = [
                                                $record->Status =>
                                                    $record->Status === 'disetujui'
                                                    ? 'Disetujui'
                                                    : 'Ditolak',

                                                ...$options,
                                            ];
                                        }

                                        return $options;
                                    }

                                    return [
                                        'diajukan' =>
                                            'Diajukan',

                                        'disetujui' =>
                                            'Disetujui',

                                        'ditolak' =>
                                            'Ditolak',

                                        'diproses' =>
                                            'Diproses',

                                        'selesai' =>
                                            'Selesai',

                                        'dibatalkan' =>
                                            'Dibatalkan',
                                    ];
                                }
                            )

                            ->disableOptionWhen(
                                function ($value, $record): bool {

                                    if (
                                        !$record
                                        ||
                                        $record
                                            ->approval
                                            ?->status !== 'approved'
                                    ) {
                                        return false;
                                    }

                                    return in_array(
                                        $value,
                                        [
                                            'diajukan',
                                            'disetujui',
                                            'ditolak',
                                        ],
                                        true
                                    );
                                }
                            )

                            ->required()

                            ->disabled(
                                function ($record) use (
                                    $isCompletedAndLocked,
                                    $isRejectedAndLocked
                                ): bool {

                                    if (
                                        $isCompletedAndLocked(
                                            $record
                                        )
                                    ) {
                                        return true;
                                    }

                                    if (
                                        $isRejectedAndLocked(
                                            $record
                                        )
                                    ) {
                                        return true;
                                    }

                                    if (!$record) {
                                        return false;
                                    }

                                    return
                                        $record
                                            ->approval
                                            ?->status
                                        !== 'approved';
                                }
                            )

                            ->dehydrated()

                            ->helperText(
                                function ($record) use (
                                    $isCompletedAndLocked,
                                    $isRejectedAndLocked
                                ): ?string {

                                    if (!$record) {
                                        return null;
                                    }

                                    if (
                                        $isCompletedAndLocked(
                                            $record
                                        )
                                    ) {

                                        return
                                            'Request sudah selesai dan tidak dapat diedit lagi. Hanya super_admin yang dapat mengubahnya.';
                                    }

                                    $approvalStatus =
                                        $record
                                            ->approval
                                            ?->status;

                                    if (
                                        $isRejectedAndLocked(
                                            $record
                                        )
                                    ) {

                                        return
                                            'Request telah ditolak oleh Kepala Bagian dan tidak dapat diedit lagi. Hanya super_admin yang dapat mengubahnya.';
                                    }

                                    if (
                                        $approvalStatus === 'pending'
                                    ) {

                                        return
                                            'Status belum dapat diubah karena masih menunggu persetujuan Kepala Bagian.';
                                    }

                                    if (
                                        $approvalStatus !== 'approved'
                                    ) {

                                        return
                                            'Request belum mendapatkan persetujuan Kepala Bagian.';
                                    }

                                    return
                                        'Request sudah disetujui dan dapat diproses oleh IT.';
                                }
                            )

                            ->live()

                            ->afterStateUpdated(
                                function (
                                    $state,
                                    callable $set
                                ): void {

                                    if (
                                        auth()->check()
                                    ) {

                                        $set(
                                            'UserPenyelesaiID',
                                            auth()->id()
                                        );
                                    }

                                    if (
                                        $state === 'selesai'
                                    ) {

                                        $set(
                                            'TanggalSelesai',
                                            now()
                                        );
                                    }
                                }
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | RENCANA SELESAI
                        |--------------------------------------------------------------------------
                        */

                        DatePicker::make(
                            'RencanaSelesai'
                        )
                            ->label(
                                'Rencana Selesai'
                            )
                            ->nullable()
                            ->disabled(
                                $isRequestLocked
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | TANGGAL SELESAI
                        |--------------------------------------------------------------------------
                        */

                        DatePicker::make(
                            'TanggalSelesai'
                        )
                            ->label(
                                'Tanggal Selesai'
                            )
                            ->format(
                                'Y-m-d'
                            )
                            ->displayFormat(
                                'd M Y'
                            )
                            ->nullable()
                            ->disabled(
                                $isRequestLocked
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | CATATAN PENYELESAIAN
                        |--------------------------------------------------------------------------
                        */

                        Textarea::make(
                            'CatatanPenyelesaian'
                        )
                            ->label(
                                'Catatan Penyelesaian'
                            )
                            ->rows(6)
                            ->nullable()
                            ->disabled(
                                $isRequestLocked
                            )
                            ->columnSpanFull(),

                        /*
                        |--------------------------------------------------------------------------
                        | SERAH TERIMA
                        |--------------------------------------------------------------------------
                        */

                        TextInput::make(
                            'SerahTerima'
                        )
                            ->label(
                                'Serah Terima'
                            )
                            ->formatStateUsing(
                                function ($state, $record): string {

                                    return
                                        $record?->SerahTerima
                                        ? 'Sudah Serah Terima'
                                        : 'Belum Serah Terima';
                                }
                            )
                            ->disabled()
                            ->dehydrated(false),

                        /*
                        |--------------------------------------------------------------------------
                        | TANGGAL SERAH TERIMA
                        |--------------------------------------------------------------------------
                        */

                        DateTimePicker::make(
                            'TanggalSerahTerima'
                        )
                            ->label(
                                'Tanggal Serah Terima'
                            )
                            ->disabled()
                            ->dehydrated(false)
                            ->nullable(),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | CATATAN BAGIAN TERKAIT
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Catatan Bagian Terkait'
                )
                    ->schema([

                        Textarea::make(
                            'related_user_notes_display'
                        )
                            ->label(
                                'Catatan'
                            )
                            ->formatStateUsing(
                                function ($state, $record): string {

                                    if (!$record) {
                                        return '-';
                                    }

                                    $notes = $record
                                        ->relatedUserNotes()
                                        ->with([
                                            'user.karyawan.departemen',
                                        ])
                                        ->latest()
                                        ->get();

                                    if (
                                        $notes->isEmpty()
                                    ) {

                                        return
                                            'Belum ada catatan.';
                                    }

                                    return $notes
                                        ->map(
                                            function ($note): string {

                                                $user =
                                                    $note->user;

                                                $nik =
                                                    $user?->NIK
                                                    ?? '-';

                                                $nama =
                                                    $user?->karyawan?->Nama
                                                    ?? $user?->name
                                                    ?? '-';

                                                $dept =
                                                    $user
                                                        ?->karyawan
                                                        ?->departemen
                                                        ?->NamaDept
                                                    ?? $user
                                                        ?->karyawan
                                                        ?->departemen
                                                        ?->NamaDepartemen
                                                    ?? '-';

                                                $tanggal =
                                                    $note->created_at
                                                        ?->format(
                                                            'd/m/Y H:i'
                                                        )
                                                    ?? '-';

                                                $catatan =
                                                    trim(
                                                        (string)
                                                        $note->catatan
                                                    );

                                                return
                                                    "[{$tanggal}] "
                                                    . "{$nik} | {$nama} | {$dept}\n"
                                                    . (
                                                        $catatan !== ''
                                                        ? $catatan
                                                        : '-'
                                                    );
                                            }
                                        )
                                        ->implode(
                                            "\n\n--------------------\n\n"
                                        );
                                }
                            )
                            ->rows(12)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->columnSpanFull(),

            ]);
    }
}
