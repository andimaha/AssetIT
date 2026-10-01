<?php

namespace App\Filament\Resources\ItRequests\Pages;

use App\Filament\Resources\ItRequests\ItRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditItRequest extends EditRecord
{
    protected static string $resource =
        ItRequestResource::class;

    /*
    |--------------------------------------------------------------------------
    | CEK SUPER ADMIN
    |--------------------------------------------------------------------------
    |
    | Hanya super_admin yang boleh bypass business-rule lock.
    |
    */

    protected function isSuperAdmin(): bool
    {
        return
            auth()->check()
            &&
            auth()->user()->hasRole('super_admin');
    }

    /*
    |--------------------------------------------------------------------------
    | CEK IT STAFF
    |--------------------------------------------------------------------------
    |
    | IT Staff dapat memproses request setelah approval Kepala Bagian
    | berstatus approved.
    |
    | Kita cek ROLE dan PERMISSION agar tetap kompatibel dengan konfigurasi
    | Spatie Permission yang digunakan aplikasi.
    |
    */

    protected function isItStaff(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        return
            $user->hasRole('it_staff')
            ||
            $user->can('itrequest.update');
    }

    /*
    |--------------------------------------------------------------------------
    | CEK REQUEST SUDAH SELESAI
    |--------------------------------------------------------------------------
    |
    | Jika sudah selesai:
    |
    | - User biasa   => LOCK
    | - IT Staff     => LOCK
    | - Kepala Bagian => LOCK
    | - User permission => LOCK
    | - super_admin  => BOLEH BYPASS
    |
    */

    protected function isCompletedAndLocked(): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }

        return
            $this->record?->Status === 'selesai';
    }

    /*
    |--------------------------------------------------------------------------
    | CEK APPROVAL DITOLAK
    |--------------------------------------------------------------------------
    |
    | Jika approval sudah rejected:
    |
    | Semua user LOCK kecuali super_admin.
    |
    */

    protected function isRejectedAndLocked(): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }

        return
            $this->record
                ?->approval
                ?->status === 'rejected';
    }

    /*
    |--------------------------------------------------------------------------
    | CEK REQUEST TERKUNCI
    |--------------------------------------------------------------------------
    */

    protected function isRequestLocked(): bool
    {
        return
            $this->isCompletedAndLocked()
            ||
            $this->isRejectedAndLocked();
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI
    |--------------------------------------------------------------------------
    */

    protected function notifyAndHalt(
        string $title,
        string $body
    ): void {
        Notification::make()
            ->danger()
            ->title($title)
            ->body($body)
            ->persistent()
            ->send();

        $this->halt();
    }

    /*
    |--------------------------------------------------------------------------
    | HEADER ACTIONS
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make()

                /*
                |--------------------------------------------------------------------------
                | REQUEST TERKUNCI TIDAK BOLEH DIHAPUS
                |--------------------------------------------------------------------------
                */

                ->disabled(
                    fn (): bool =>
                        $this->isRequestLocked()
                )

                /*
                |--------------------------------------------------------------------------
                | PERMISSION DELETE TETAP DIHORMATI
                |--------------------------------------------------------------------------
                */

                ->visible(
                    fn ($record) =>
                        ItRequestResource::canDelete($record)
                )

                ->before(
                    function (): void {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST SUDAH SELESAI
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $this->isCompletedAndLocked()
                        ) {

                            $this->notifyAndHalt(
                                'Request sudah selesai',
                                'Request yang sudah selesai tidak dapat dihapus. Hanya super_admin yang dapat mengubah atau menghapus request ini.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | APPROVAL DITOLAK
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $this->isRejectedAndLocked()
                        ) {

                            $this->notifyAndHalt(
                                'Request telah ditolak',
                                'Request yang telah ditolak oleh Kepala Bagian tidak dapat dihapus atau diedit. Hanya super_admin yang dapat mengubah atau menghapus request ini.'
                            );
                        }
                    }
                ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI SEBELUM SAVE
    |--------------------------------------------------------------------------
    |
    | Server-side protection.
    |
    */

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        $record = $this->record;

        /*
        |--------------------------------------------------------------------------
        | REQUEST SUDAH SELESAI
        |--------------------------------------------------------------------------
        */

        if (
            $this->isCompletedAndLocked()
        ) {

            $this->notifyAndHalt(
                'Request sudah selesai',
                'Request yang sudah selesai tidak dapat diedit lagi. Hanya super_admin yang dapat mengubahnya.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | APPROVAL DITOLAK
        |--------------------------------------------------------------------------
        */

        if (
            $this->isRejectedAndLocked()
        ) {

            $this->notifyAndHalt(
                'Request telah ditolak',
                'Request yang telah ditolak oleh Kepala Bagian tidak dapat diedit lagi. Hanya super_admin yang dapat mengubahnya.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS LAMA
        |--------------------------------------------------------------------------
        */

        $oldStatus =
            (string) $record->Status;

        /*
        |--------------------------------------------------------------------------
        | STATUS BARU
        |--------------------------------------------------------------------------
        */

        $newStatus =
            $data['Status']
            ?? $oldStatus;

        /*
        |--------------------------------------------------------------------------
        | STATUS APPROVAL
        |--------------------------------------------------------------------------
        */

        $approvalStatus =
            $record
                ->approval
                ?->status;

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        |
        | super_admin tetap boleh bypass business-rule tertentu.
        |
        | Tetapi jika approval rejected, bagian di atas sudah membolehkan
        | bypass karena isRejectedAndLocked() mengembalikan false.
        |
        */

        if ($this->isSuperAdmin()) {

            /*
            |--------------------------------------------------------------------------
            | Jika super_admin mengubah menjadi selesai
            |--------------------------------------------------------------------------
            */

            if (
                $newStatus === 'selesai'
            ) {

                if (
                    blank(
                        $data['UserPenyelesaiID']
                        ?? null
                    )
                    &&
                    auth()->check()
                ) {
                    $data['UserPenyelesaiID'] =
                        auth()->id();
                }

                if (
                    blank(
                        $data['TanggalSelesai']
                        ?? null
                    )
                ) {
                    $data['TanggalSelesai'] =
                        now()->format('Y-m-d');
                }
            }

            return $data;
        }

        /*
        |--------------------------------------------------------------------------
        | CEK IT STAFF
        |--------------------------------------------------------------------------
        |
        | Untuk perubahan status pekerjaan, user harus merupakan:
        |
        | - role it_staff
        | ATAU
        | - memiliki permission itrequest.update
        |
        */

        $isItStaff = $this->isItStaff();

        /*
        |--------------------------------------------------------------------------
        | APPROVAL REJECTED
        |--------------------------------------------------------------------------
        |
        | Normalnya sudah dihentikan di atas.
        |
        */

        if (
            $approvalStatus === 'rejected'
        ) {

            $this->notifyAndHalt(
                'Request ditolak',
                'Request yang ditolak Kepala Bagian tidak dapat diproses atau diedit.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | BELUM APPROVED
        |--------------------------------------------------------------------------
        |
        | IT Staff tidak boleh mengubah status pekerjaan sebelum
        | Kepala Bagian memberikan approval.
        |
        */

        if (
            $approvalStatus !== 'approved'
            &&
            $newStatus !== $oldStatus
        ) {

            $message = match ($approvalStatus) {

                'pending' =>
                    'Permintaan masih menunggu persetujuan Kepala Bagian.',

                'rejected' =>
                    'Permintaan telah ditolak oleh Kepala Bagian.',

                default =>
                    'Permintaan belum mendapatkan persetujuan Kepala Bagian.',
            };

            Notification::make()
                ->danger()
                ->title(
                    'Status tidak dapat diubah'
                )
                ->body($message)
                ->persistent()
                ->send();

            $data['Status'] =
                $oldStatus;

            return $data;
        }

        /*
        |--------------------------------------------------------------------------
        | APPROVAL APPROVED
        |--------------------------------------------------------------------------
        |
        | Di tahap ini IT Staff boleh bekerja pada request.
        |
        */

        if (
            $approvalStatus === 'approved'
        ) {

            /*
            |--------------------------------------------------------------------------
            | STATUS YANG DIIZINKAN
            |--------------------------------------------------------------------------
            */

            $allowedStatuses = [
                'diproses',
                'selesai',
                'dibatalkan',
            ];

            /*
            |--------------------------------------------------------------------------
            | CEGAH STATUS LAMA/STATUS INVALID
            |--------------------------------------------------------------------------
            */

            if (
                $newStatus !== $oldStatus
                &&
                !in_array(
                    $newStatus,
                    $allowedStatuses,
                    true
                )
            ) {

                Notification::make()
                    ->danger()
                    ->title(
                        'Status tidak dapat dipilih'
                    )
                    ->body(
                        'Setelah disetujui Kepala Bagian, status hanya dapat diubah menjadi Diproses, Selesai, atau Dibatalkan.'
                    )
                    ->persistent()
                    ->send();

                $data['Status'] =
                    $oldStatus;

                return $data;
            }

            /*
            |--------------------------------------------------------------------------
            | PERUBAHAN STATUS PEKERJAAN HARUS OLEH IT STAFF
            |--------------------------------------------------------------------------
            |
            | Ini penting agar user lain yang kebetulan memiliki akses edit
            | tidak dapat mengubah status pekerjaan.
            |
            */

            if (
                $newStatus !== $oldStatus
                &&
                in_array(
                    $newStatus,
                    $allowedStatuses,
                    true
                )
                &&
                !$isItStaff
            ) {

                Notification::make()
                    ->danger()
                    ->title(
                        'Tidak memiliki akses'
                    )
                    ->body(
                        'Hanya IT Staff atau user dengan permission itrequest.update yang dapat memproses request.'
                    )
                    ->persistent()
                    ->send();

                $data['Status'] =
                    $oldStatus;

                return $data;
            }

            /*
            |--------------------------------------------------------------------------
            | PENYELESAI
            |--------------------------------------------------------------------------
            |
            | Jika IT Staff memilih:
            |
            | - diproses
            | - selesai
            |
            | maka UserPenyelesaiID otomatis diisi user login.
            |
            */

            if (
                in_array(
                    $newStatus,
                    [
                        'diproses',
                        'selesai',
                    ],
                    true
                )
                &&
                $isItStaff
                &&
                auth()->check()
            ) {

                $data['UserPenyelesaiID'] =
                    auth()->id();
            }

            /*
            |--------------------------------------------------------------------------
            | TANGGAL SELESAI
            |--------------------------------------------------------------------------
            */

            if (
                $newStatus === 'selesai'
            ) {

                $data['TanggalSelesai'] =
                    $data['TanggalSelesai']
                    ?? now()->format('Y-m-d');
            }

            /*
            |--------------------------------------------------------------------------
            | JIKA DIBATALKAN
            |--------------------------------------------------------------------------
            |
            | UserPenyelesaiID tetap dipertahankan.
            |
            */

            if (
                $newStatus === 'dibatalkan'
                &&
                blank(
                    $data['UserPenyelesaiID']
                    ?? null
                )
                &&
                $record->UserPenyelesaiID
            ) {

                $data['UserPenyelesaiID'] =
                    $record->UserPenyelesaiID;
            }
        }

        return $data;
    }
}
