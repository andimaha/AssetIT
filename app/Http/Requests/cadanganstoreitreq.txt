<?php

namespace App\Http\Requests;

use App\Models\ItRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItRequestRequest extends FormRequest
{
    /**
     * ==========================================================
     * AUTHORIZE
     * ==========================================================
     *
     * User harus memiliki izin membuat ItRequest.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', ItRequest::class) ?? false;
    }

    /**
     * ==========================================================
     * RULES
     * ==========================================================
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | JENIS PERMINTAAN
            |--------------------------------------------------------------------------
            |
            | Field ini dikirim sebagai array ID jenis permintaan.
            |
            */

            'jenis_permintaan' => [
                'required',
                'array',
                'min:1',
            ],

            'jenis_permintaan.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('mstjenispermintaan', 'id')
                    ->where(function ($query) {
                        $query->where('is_active', true);
                    }),
            ],


            /*
            |--------------------------------------------------------------------------
            | PERMINTAAN
            |--------------------------------------------------------------------------
            |
            | Isi utama permintaan IT.
            |
            */

            'Permintaan' => [
                'required',
                'string',
                'max:65535',
            ],


            /*
            |--------------------------------------------------------------------------
            | KETERANGAN
            |--------------------------------------------------------------------------
            |
            | Keterangan tambahan bersifat opsional.
            |
            */

            'Keterangan' => [
                'nullable',
                'string',
                'max:65535',
            ],


            /*
            |--------------------------------------------------------------------------
            | ASSET
            |--------------------------------------------------------------------------
            |
            | Asset bersifat opsional.
            |
            | Nilai yang dikirim adalah NoAssetIT, bukan ID mstasset.
            |
            */

            'assets' => [
                'nullable',
                'array',
            ],

            'assets.*' => [
                'required',
                'string',
                'distinct',
                Rule::exists('mstasset', 'NoAssetIT'),
            ],


            /*
            |--------------------------------------------------------------------------
            | RELATED USERS
            |--------------------------------------------------------------------------
            |
            | User terkait bersifat opsional.
            |
            | Hanya user yang mempunyai NIK yang diperbolehkan.
            |
            */

            'related_users' => [
                'nullable',
                'array',
            ],

            'related_users.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('users', 'id')
                    ->whereNotNull('NIK'),
            ],
        ];
    }

    /**
     * ==========================================================
     * MESSAGES
     * ==========================================================
     */
    public function messages(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | JENIS PERMINTAAN
            |--------------------------------------------------------------------------
            */

            'jenis_permintaan.required' =>
                'Jenis permintaan wajib dipilih.',

            'jenis_permintaan.array' =>
                'Format jenis permintaan tidak valid.',

            'jenis_permintaan.min' =>
                'Minimal satu jenis permintaan harus dipilih.',

            'jenis_permintaan.*.required' =>
                'Jenis permintaan wajib dipilih.',

            'jenis_permintaan.*.integer' =>
                'ID jenis permintaan tidak valid.',

            'jenis_permintaan.*.distinct' =>
                'Jenis permintaan tidak boleh dipilih lebih dari satu kali.',

            'jenis_permintaan.*.exists' =>
                'Jenis permintaan yang dipilih tidak valid atau sudah tidak aktif.',


            /*
            |--------------------------------------------------------------------------
            | PERMINTAAN
            |--------------------------------------------------------------------------
            */

            'Permintaan.required' =>
                'Permintaan wajib diisi.',

            'Permintaan.string' =>
                'Permintaan harus berupa teks.',

            'Permintaan.max' =>
                'Permintaan terlalu panjang.',


            /*
            |--------------------------------------------------------------------------
            | KETERANGAN
            |--------------------------------------------------------------------------
            */

            'Keterangan.string' =>
                'Keterangan harus berupa teks.',

            'Keterangan.max' =>
                'Keterangan terlalu panjang.',


            /*
            |--------------------------------------------------------------------------
            | ASSET
            |--------------------------------------------------------------------------
            */

            'assets.array' =>
                'Format asset tidak valid.',

            'assets.*.required' =>
                'Asset yang dipilih tidak valid.',

            'assets.*.string' =>
                'Nomor asset harus berupa teks.',

            'assets.*.distinct' =>
                'Asset tidak boleh dipilih lebih dari satu kali.',

            'assets.*.exists' =>
                'Asset IT yang dipilih tidak ditemukan.',


            /*
            |--------------------------------------------------------------------------
            | RELATED USERS
            |--------------------------------------------------------------------------
            */

            'related_users.array' =>
                'Format user terkait tidak valid.',

            'related_users.*.required' =>
                'User terkait tidak valid.',

            'related_users.*.integer' =>
                'ID user terkait tidak valid.',

            'related_users.*.distinct' =>
                'User terkait tidak boleh dipilih lebih dari satu kali.',

            'related_users.*.exists' =>
                'User terkait tidak valid atau belum memiliki NIK.',
        ];
    }
}
