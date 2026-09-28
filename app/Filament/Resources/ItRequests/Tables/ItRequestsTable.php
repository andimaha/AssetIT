<?php

namespace App\Filament\Resources\ItRequests\Tables;

use App\Filament\Resources\ItRequests\ItRequestResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            /*
            |--------------------------------------------------------------------------
            | RECORD URL
            |--------------------------------------------------------------------------
            |
            | Hanya user yang mempunyai permission UPDATE yang diarahkan
            | ke halaman Edit ketika klik row.
            |
            */

            ->recordUrl(
                fn ($record) =>
                    auth()->check()
                    &&
                    ItRequestResource::canEdit($record)
                        ? ItRequestResource::getUrl(
                            'edit',
                            [
                                'record' => $record,
                            ]
                        )
                        : null
            )

            /*
            |--------------------------------------------------------------------------
            | SORT
            |--------------------------------------------------------------------------
            */

            ->defaultSort(
                'IDRequest',
                'desc'
            )

            /*
            |--------------------------------------------------------------------------
            | PAGINATION
            |--------------------------------------------------------------------------
            */

            ->paginated([
                10,
                25,
                50,
                100,
                250,
                'all',
            ])

            ->paginationPageOptions([
                10,
                25,
                50,
                100,
                250,
                'all',
            ])

            ->defaultPaginationPageOption('all')

            /*
            |--------------------------------------------------------------------------
            | EAGER LOAD
            |--------------------------------------------------------------------------
            */

            ->modifyQueryUsing(
                function ($query) {

                    $query->with([

                        /*
                        |--------------------------------------------------------------------------
                        | PEMOHON
                        |--------------------------------------------------------------------------
                        */

                        'pemohon.karyawan.departemen',

                        'pemohon.karyawan.lokasi',

                        /*
                        |--------------------------------------------------------------------------
                        | KEPALA BAGIAN
                        |--------------------------------------------------------------------------
                        */

                        'pemohon.karyawan.kepalaBagian.user',

                        /*
                        |--------------------------------------------------------------------------
                        | ASSET
                        |--------------------------------------------------------------------------
                        */

                        'assets',

                        /*
                        |--------------------------------------------------------------------------
                        | JENIS PERMINTAAN
                        |--------------------------------------------------------------------------
                        */

                        'jenisPermintaan',

                        /*
                        |--------------------------------------------------------------------------
                        | PENYELESAI
                        |--------------------------------------------------------------------------
                        */

                        'penyelesai.karyawan',

                        /*
                        |--------------------------------------------------------------------------
                        | USER TERKAIT
                        |--------------------------------------------------------------------------
                        */

                        'relatedUsers.karyawan.departemen',

                        /*
                        |--------------------------------------------------------------------------
                        | APPROVAL
                        |--------------------------------------------------------------------------
                        */

                        'approval.approver.karyawan',

                    ]);

                }
            )

            /*
            |--------------------------------------------------------------------------
            | GLOBAL SEARCH
            |--------------------------------------------------------------------------
            */

            ->searchable()

            ->modifyQueryUsing(
                function ($query) {

                    $search =
                        request()->query('tableSearch')
                        ??
                        request()->query('search');

                    if (blank($search)) {
                        return;
                    }

                    $search = trim($search);

                    $query->where(
                        function ($query) use ($search) {

                            /*
                            |--------------------------------------------------------------------------
                            | NO REQUEST
                            |--------------------------------------------------------------------------
                            */

                            $query->where(
                                'it_requests.NoRequest',
                                'like',
                                "%{$search}%"
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | PEMOHON
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhereHas(
                                'pemohon',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
                                                ->where(
                                                    'users.name',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.NIK',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.email',
                                                    'like',
                                                    "%{$search}%"
                                                );

                                        }
                                    );

                                }
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | NAMA KARYAWAN PEMOHON
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhereHas(
                                'pemohon.karyawan',
                                function ($query) use ($search) {

                                    $query->where(
                                        'mstkaryawan.Nama',
                                        'like',
                                        "%{$search}%"
                                    );

                                }
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | DEPARTEMEN
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhereHas(
                                'pemohon.karyawan.departemen',
                                function ($query) use ($search) {

                                    $query->where(
                                        'mstdepartemen.NamaDept',
                                        'like',
                                        "%{$search}%"
                                    );

                                }
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | JENIS PERMINTAAN
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhereHas(
                                'jenisPermintaan',
                                function ($query) use ($search) {

                                    $query->where(
                                        'mstjenispermintaan.name',
                                        'like',
                                        "%{$search}%"
                                    );

                                }
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | PERMINTAAN
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhere(
                                'it_requests.Permintaan',
                                'like',
                                "%{$search}%"
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | KETERANGAN
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhere(
                                'it_requests.Keterangan',
                                'like',
                                "%{$search}%"
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | ASSET
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhereHas(
                                'assets',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
                                                ->where(
                                                    'mstasset.NoAssetIT',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'mstasset.NoAssetSAP',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'mstasset.Nama',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'mstasset.SN',
                                                    'like',
                                                    "%{$search}%"
                                                );

                                        }
                                    );

                                }
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | BAGIAN TERKAIT
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhereHas(
                                'relatedUsers',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
                                                ->where(
                                                    'users.name',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.NIK',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.email',
                                                    'like',
                                                    "%{$search}%"
                                                );

                                        }
                                    );

                                }
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | KEPALA BAGIAN
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhereHas(
                                'pemohon.karyawan.kepalaBagian',
                                function ($query) use ($search) {

                                    $query->where(
                                        'mstkaryawan.Nama',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'mstkaryawan.NIK',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhereHas(
                                        'user',
                                        function ($userQuery) use ($search) {

                                            $userQuery->where(
                                                'users.name',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'users.email',
                                                'like',
                                                "%{$search}%"
                                            );

                                        }
                                    );

                                }
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | APPROVER
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhereHas(
                                'approval.approver',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
                                                ->where(
                                                    'users.name',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.NIK',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.email',
                                                    'like',
                                                    "%{$search}%"
                                                );

                                        }
                                    );

                                }
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | PENYELESAI
                            |--------------------------------------------------------------------------
                            */

                            $query->orWhereHas(
                                'penyelesai',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
                                                ->where(
                                                    'users.name',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.NIK',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.email',
                                                    'like',
                                                    "%{$search}%"
                                                );

                                        }
                                    );

                                }
                            );

                        }
                    );

                }
            )

            /*
            |--------------------------------------------------------------------------
            | COLUMNS
            |--------------------------------------------------------------------------
            */

            ->columns([

                TextColumn::make('No')
                    ->label('NO')
                    ->rowIndex()
                    ->weight('bold')
                    ->width('70px'),

                TextColumn::make('NoRequest')
                    ->label('NO. REQUEST')
                    ->searchable(
                        query: function (
                            $query,
                            string $search
                        ): void {

                            $query->where(
                                'it_requests.NoRequest',
                                'like',
                                "%{$search}%"
                            );

                        }
                    )
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->width('150px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make('pemohon.name')
                    ->label('PEMOHON')
                    ->formatStateUsing(
                        function (
                            $state,
                            $record
                        ) {

                            $nama =
                                $record
                                    ->pemohon
                                    ?->karyawan
                                    ?->Nama
                                ??
                                $record
                                    ->pemohon
                                    ?->name
                                ??
                                '-';

                            $nik =
                                $record
                                    ->pemohon
                                    ?->NIK
                                ??
                                '-';

                            return
                                $nama
                                . ' | NIK: '
                                . $nik;

                        }
                    )
                    ->searchable(
                        query: function (
                            $query,
                            string $search
                        ): void {

                            $query->whereHas(
                                'pemohon',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
                                                ->where(
                                                    'users.name',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.NIK',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.email',
                                                    'like',
                                                    "%{$search}%"
                                                );

                                        }
                                    );

                                }
                            );

                        }
                    )
                    ->sortable()
                    ->width('220px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make(
                    'pemohon.karyawan.departemen.NamaDept'
                )
                    ->label('DEPARTEMEN')
                    ->formatStateUsing(
                        function (
                            $state,
                            $record
                        ) {

                            return
                                $record
                                    ->pemohon
                                    ?->karyawan
                                    ?->departemen
                                    ?->NamaDept
                                ??
                                '-';

                        }
                    )
                    ->searchable(
                        query: function (
                            $query,
                            string $search
                        ): void {

                            $query->whereHas(
                                'pemohon.karyawan.departemen',
                                function ($query) use ($search) {

                                    $query->where(
                                        'mstdepartemen.NamaDept',
                                        'like',
                                        "%{$search}%"
                                    );

                                }
                            );

                        }
                    )
                    ->width('180px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make(
                    'jenis_permintaan_display'
                )
                    ->label('JENIS')
                    ->state(
                        function ($record) {

                            if (
                                ! $record->jenisPermintaan
                                ||
                                $record->jenisPermintaan->isEmpty()
                            ) {
                                return '-';
                            }

                            return
                                $record
                                    ->jenisPermintaan
                                    ->pluck('name')
                                    ->filter(
                                        fn ($name) =>
                                            filled($name)
                                    )
                                    ->unique()
                                    ->values()
                                    ->implode(' | ');

                        }
                    )
                    ->badge()
                    ->color('violet')
                    ->searchable(
                        query: function (
                            $query,
                            string $search
                        ): void {

                            $query->whereHas(
                                'jenisPermintaan',
                                function ($query) use ($search) {

                                    $query->where(
                                        'mstjenispermintaan.name',
                                        'like',
                                        "%{$search}%"
                                    );

                                }
                            );

                        }
                    )
                    ->width('180px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make('Permintaan')
                    ->label('PERMINTAAN')
                    ->limit(80)
                    ->wrap()
                    ->lineClamp(5)
                    ->width('300px')
                    ->searchable(
                        query: function (
                            $query,
                            string $search
                        ): void {

                            $query->where(
                                'it_requests.Permintaan',
                                'like',
                                "%{$search}%"
                            );

                        }
                    ),

                TextColumn::make('assets')
                    ->label('ASSET IT')
                    ->formatStateUsing(
                        function (
                            $state,
                            $record
                        ) {

                            if (
                                ! $record->assets
                                ||
                                $record->assets->isEmpty()
                            ) {
                                return '-';
                            }

                            return
                                $record
                                    ->assets
                                    ->map(
                                        function ($asset) {

                                            $noAsset =
                                                $asset->NoAssetIT
                                                ??
                                                '-';

                                            $namaAsset =
                                                $asset->Nama
                                                ??
                                                '';

                                            if (
                                                $namaAsset === ''
                                            ) {
                                                return $noAsset;
                                            }

                                            return
                                                $noAsset
                                                . ' | '
                                                . $namaAsset;

                                        }
                                    )
                                    ->implode(', ');

                        }
                    )
                    ->searchable(
                        query: function (
                            $query,
                            string $search
                        ): void {

                            $query->whereHas(
                                'assets',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
                                                ->where(
                                                    'mstasset.NoAssetIT',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'mstasset.NoAssetSAP',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'mstasset.Nama',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'mstasset.SN',
                                                    'like',
                                                    "%{$search}%"
                                                );

                                        }
                                    );

                                }
                            );

                        }
                    )
                    ->width('250px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make(
                    'relatedUsers.name'
                )
                    ->label('BAGIAN TERKAIT')
                    ->formatStateUsing(
                        function (
                            $state,
                            $record
                        ) {

                            if (
                                ! $record->relatedUsers
                                ||
                                $record
                                    ->relatedUsers
                                    ->isEmpty()
                            ) {
                                return '-';
                            }

                            return
                                $record
                                    ->relatedUsers
                                    ->map(
                                        function ($user) {

                                            $nik =
                                                $user->NIK
                                                ??
                                                '-';

                                            $nama =
                                                $user
                                                    ->karyawan
                                                    ?->Nama
                                                ??
                                                $user->name
                                                ??
                                                '-';

                                            $dept =
                                                $user
                                                    ->karyawan
                                                    ?->departemen
                                                    ?->NamaDept
                                                ??
                                                '-';

                                            return
                                                $nik
                                                . ' - '
                                                . $nama
                                                . ' - '
                                                . $dept;

                                        }
                                    )
                                    ->implode(', ');

                        }
                    )
                    ->searchable(
                        query: function (
                            $query,
                            string $search
                        ): void {

                            $query->whereHas(
                                'relatedUsers',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
                                                ->where(
                                                    'users.name',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.NIK',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.email',
                                                    'like',
                                                    "%{$search}%"
                                                );

                                        }
                                    );

                                }
                            );

                        }
                    )
                    ->placeholder('-')
                    ->width('300px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make(
                    'pemohon.karyawan.kepalaBagian.user.name'
                )
                    ->label('KEPALA BAGIAN')
                    ->formatStateUsing(
                        function (
                            $state,
                            $record
                        ) {

                            $kepalaBagian =
                                $record
                                    ->pemohon
                                    ?->karyawan
                                    ?->kepalaBagian;

                            if (! $kepalaBagian) {
                                return '-';
                            }

                            $nama =
                                $kepalaBagian
                                    ->Nama
                                ??
                                $kepalaBagian
                                    ->user
                                    ?->name
                                ??
                                '-';

                            $nik =
                                $kepalaBagian
                                    ->NIK
                                ??
                                '-';

                            return
                                $nama
                                . ' | NIK: '
                                . $nik;

                        }
                    )
                    ->searchable(
                        query: function (
                            $query,
                            string $search
                        ): void {

                            $query->whereHas(
                                'pemohon.karyawan.kepalaBagian',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
                                                ->where(
                                                    'mstkaryawan.Nama',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'mstkaryawan.NIK',
                                                    'like',
                                                    "%{$search}%"
                                                );

                                        }
                                    )
                                    ->orWhereHas(
                                        'user',
                                        function ($userQuery) use ($search) {

                                            $userQuery->where(
                                                function (
                                                    $userQuery
                                                ) use ($search) {

                                                    $userQuery
                                                        ->where(
                                                            'users.name',
                                                            'like',
                                                            "%{$search}%"
                                                        )
                                                        ->orWhere(
                                                            'users.email',
                                                            'like',
                                                            "%{$search}%"
                                                        );

                                                }
                                            );

                                        }
                                    );

                                }
                            );

                        }
                    )
                    ->width('240px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make(
                    'approval.status'
                )
                    ->label(
                        'APPROVAL KEPALA BAGIAN'
                    )
                    ->badge()
                    ->formatStateUsing(
                        function ($state) {

                            return match ($state) {

                                'pending' =>
                                    'Menunggu Persetujuan',

                                'approved' =>
                                    'Disetujui',

                                'rejected' =>
                                    'Ditolak',

                                default =>
                                    $state
                                    ?
                                    ucfirst($state)
                                    :
                                    'Belum Ada',

                            };

                        }
                    )
                    ->color(
                        function ($state) {

                            return match ($state) {

                                'pending' =>
                                    'warning',

                                'approved' =>
                                    'success',

                                'rejected' =>
                                    'danger',

                                default =>
                                    'gray',

                            };

                        }
                    )
                    ->placeholder('Belum Ada')
                    ->width('220px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make(
                    'approval.approved_at'
                )
                    ->label(
                        'TANGGAL APPROVAL'
                    )
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->sortable()
                    ->width('150px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make('penyelesai.name')
                    ->label('PENYELESAI')
                    ->formatStateUsing(
                        function (
                            $state,
                            $record
                        ) {

                            return
                                $record
                                    ->penyelesai
                                    ?->karyawan
                                    ?->Nama
                                ??
                                $record
                                    ->penyelesai
                                    ?->name
                                ??
                                '-';

                        }
                    )
                    ->searchable(
                        query: function (
                            $query,
                            string $search
                        ): void {

                            $query->whereHas(
                                'penyelesai',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
                                                ->where(
                                                    'users.name',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.NIK',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'users.email',
                                                    'like',
                                                    "%{$search}%"
                                                );

                                        }
                                    );

                                }
                            );

                        }
                    )
                    ->placeholder('-')
                    ->sortable()
                    ->width('220px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make('Status')
                    ->label('STATUS')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) => match ($state) {

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
                                $state
                                ?
                                ucfirst($state)
                                :
                                '-',

                        }
                    )
                    ->color(
                        fn ($state) => match ($state) {

                            'diajukan' =>
                                'warning',

                            'disetujui' =>
                                'success',

                            'diproses' =>
                                'info',

                            'selesai' =>
                                'success',

                            'ditolak' =>
                                'danger',

                            'dibatalkan' =>
                                'gray',

                            default =>
                                'gray',

                        }
                    )
                    ->sortable()
                    ->width('140px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make('SerahTerima')
                    ->label('SERAH TERIMA')
                    ->badge()
                    ->formatStateUsing(
                        function (
                            $state,
                            $record
                        ) {

                            if (
                                $record->SerahTerima === true
                            ) {
                                return 'Sudah Diterima';
                            }

                            if (
                                $record->isSelesai()
                            ) {
                                return 'Menunggu Serah Terima';
                            }

                            return 'Belum';

                        }
                    )
                    ->color(
                        function (
                            $state,
                            $record
                        ) {

                            if (
                                $record->SerahTerima === true
                            ) {
                                return 'success';
                            }

                            if (
                                $record->isSelesai()
                            ) {
                                return 'warning';
                            }

                            return 'gray';

                        }
                    )
                    ->sortable()
                    ->width('200px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make(
                    'TanggalSerahTerima'
                )
                    ->label(
                        'TANGGAL SERAH TERIMA'
                    )
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->sortable()
                    ->width('150px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make(
                    'RencanaSelesai'
                )
                    ->label(
                        'RENCANA SELESAI'
                    )
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->sortable()
                    ->width('160px')
                    ->wrap()
                    ->lineClamp(5),

                TextColumn::make('created_at')
                    ->label('DIAJUKAN')
                    ->date('d/m/Y')
                    ->sortable()
                    ->width('150px')
                    ->wrap()
                    ->lineClamp(5),

            ])

            /*
            |--------------------------------------------------------------------------
            | FILTER
            |--------------------------------------------------------------------------
            */

            ->filters([

                SelectFilter::make(
                    'jenis_permintaan'
                )
                    ->label(
                        'Jenis Permintaan'
                    )
                    ->options([

                        'hardware' =>
                            'Hardware',

                        'software' =>
                            'Software',

                        'data' =>
                            'Data',

                        'lainnya' =>
                            'Lainnya',

                    ])
                    ->multiple()
                    ->query(
                        function (
                            $query,
                            array $data
                        ) {

                            $values =
                                $data['values']
                                ?? [];

                            if (
                                empty($values)
                            ) {
                                return;
                            }

                            $query->whereHas(
                                'jenisPermintaan',
                                function ($query) use (
                                    $values
                                ) {

                                    $query->whereIn(
                                        'mstjenispermintaan.name',
                                        $values
                                    );

                                }
                            );

                        }
                    ),

                SelectFilter::make(
                    'approval_status'
                )
                    ->label(
                        'Approval Kepala Bagian'
                    )
                    ->options([

                        'pending' =>
                            'Menunggu Persetujuan',

                        'approved' =>
                            'Disetujui',

                        'rejected' =>
                            'Ditolak',

                    ])
                    ->query(
                        function (
                            $query,
                            array $data
                        ) {

                            $value =
                                $data['value']
                                ?? null;

                            if (
                                blank($value)
                            ) {
                                return;
                            }

                            $query->whereHas(
                                'approval',
                                function ($query) use (
                                    $value
                                ) {

                                    $query->where(
                                        'status',
                                        $value
                                    );

                                }
                            );

                        }
                    ),

                SelectFilter::make(
                    'Status'
                )
                    ->label(
                        'Status Request'
                    )
                    ->options([

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

                    ])
                    ->query(
                        function (
                            $query,
                            array $data
                        ) {

                            $value =
                                $data['value']
                                ?? null;

                            if (
                                blank($value)
                            ) {
                                return;
                            }

                            $query->where(
                                'it_requests.Status',
                                $value
                            );

                        }
                    ),

                SelectFilter::make(
                    'SerahTerima'
                )
                    ->label(
                        'Serah Terima'
                    )
                    ->options([

                        '1' =>
                            'Sudah Diterima',

                        '0' =>
                            'Belum / Menunggu Serah Terima',

                    ])
                    ->query(
                        function (
                            $query,
                            array $data
                        ) {

                            $value =
                                $data['value']
                                ?? null;

                            if (
                                blank($value)
                            ) {
                                return;
                            }

                            if (
                                $value === '1'
                            ) {

                                $query->where(
                                    'it_requests.SerahTerima',
                                    1
                                );

                                return;

                            }

                            if (
                                $value === '0'
                            ) {

                                $query->where(
                                    function ($query) {

                                        $query
                                            ->whereNull(
                                                'it_requests.SerahTerima'
                                            )
                                            ->orWhere(
                                                'it_requests.SerahTerima',
                                                ''
                                            )
                                            ->orWhere(
                                                'it_requests.SerahTerima',
                                                0
                                            );

                                    }
                                );

                            }

                        }
                    ),

                SelectFilter::make('overdue')
                    ->label(
                        'Rencana Selesai'
                    )
                    ->options([

                        '1' =>
                            'Lewat Rencana Selesai',

                    ])
                    ->query(
                        function (
                            $query,
                            array $data
                        ) {

                            $value =
                                $data['value']
                                ?? null;

                            if (
                                $value !== '1'
                            ) {
                                return;
                            }

                            $query
                                ->whereNotNull(
                                    'it_requests.RencanaSelesai'
                                )
                                ->whereDate(
                                    'it_requests.RencanaSelesai',
                                    '<',
                                    now()
                                )
                                ->whereNotIn(
                                    'it_requests.Status',
                                    [
                                        'selesai',
                                        'ditolak',
                                        'dibatalkan',
                                    ]
                                );

                        }
                    ),

            ])

            /*
            |--------------------------------------------------------------------------
            | RECORD ACTIONS
            |--------------------------------------------------------------------------
            |
            | PENTING:
            |
            | Jangan menggunakan role:
            |
            |   super admin
            |   staff IT
            |
            | sebagai penentu tombol.
            |
            | Gunakan permission.
            |
            | Karena BaseResource sudah menentukan:
            |
            |   itrequest.update
            |   itrequest.delete
            |
            */

            ->recordActions([

                /*
                |--------------------------------------------------------------------------
                | EDIT
                |--------------------------------------------------------------------------
                */

                EditAction::make()
                    ->visible(
                        fn ($record): bool =>
                            auth()->check()
                            &&
                            auth()
                                ->user()
                                ->can('itrequest.update')
                            &&
                            ItRequestResource::canEdit(
                                $record
                            )
                    ),

                /*
                |--------------------------------------------------------------------------
                | DELETE
                |--------------------------------------------------------------------------
                */

                DeleteAction::make()
                    ->visible(
                        fn ($record): bool =>
                            auth()->check()
                            &&
                            auth()
                                ->user()
                                ->can('itrequest.delete')
                            &&
                            ItRequestResource::canDelete(
                                $record
                            )
                    ),

            ])

            /*
            |--------------------------------------------------------------------------
            | BULK ACTIONS
            |--------------------------------------------------------------------------
            */

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                        ->visible(
                            fn (): bool =>
                                auth()->check()
                                &&
                                auth()
                                    ->user()
                                    ->can(
                                        'itrequest.delete'
                                    )
                                &&
                                ItRequestResource::canDeleteAny()
                        ),

                ]),

            ]);

    }
}
