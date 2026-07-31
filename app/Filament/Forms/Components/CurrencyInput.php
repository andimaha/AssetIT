<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class CurrencyInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->prefix('Rp')
            ->numeric(false)

            // Format saat nilai diambil dari database (Edit)
            ->formatStateUsing(function ($state) {
                if (blank($state)) {
                    return null;
                }

                return number_format((float) $state, 2, ',', '.');
            })

            // Format saat user mengetik
            ->mask(RawJs::make(<<<'JS'
                $input => {
                    if (!$input) {
                        return '';
                    }

                    // Sisakan hanya angka dan koma
                    let value = $input
                        .replace(/[^\d,]/g, '')
                        .replace(/,+/g, ',');

                    let parts = value.split(',');

                    // Hilangkan pemisah ribuan lama
                    parts[0] = parts[0].replace(/\./g, '');

                    // Format ribuan
                    parts[0] = new Intl.NumberFormat('id-ID').format(parts[0]);

                    // Maksimal 2 digit desimal
                    if (parts.length > 1) {
                        parts[1] = parts[1].substring(0, 2);
                    }

                    return parts.length > 1
                        ? parts[0] + ',' + parts[1]
                        : parts[0];
                }
            JS))

            // Format saat disimpan ke database
            ->dehydrateStateUsing(function ($state) {
                if (blank($state)) {
                    return null;
                }

                // Hilangkan pemisah ribuan
                $state = str_replace('.', '', $state);

                // Ubah koma menjadi titik desimal
                $state = str_replace(',', '.', $state);

                return $state;
            });
    }
}