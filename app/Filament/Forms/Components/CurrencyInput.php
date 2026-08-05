<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class CurrencyInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->prefix('Rp')

            ->numeric(false)

            ->formatStateUsing(function ($state) {

                if (blank($state)) {
                    return null;
                }

                return number_format(
                    (float) $state,
                    2,
                    ',',
                    '.'
                );

            })

            ->extraInputAttributes([

                'x-data' => '{}',

                'x-on:input' => <<<'JS'

                    let el = $event.target;

                    let cursor = el.selectionStart;

                    let value = el.value;

                    // hapus titik ribuan lama
                    value = value.replace(/\./g, '');

                    // hanya izinkan angka dan koma
                    value = value.replace(/[^0-9,]/g, '');

                    // hanya satu koma desimal
                    let parts = value.split(',');

                    if (parts.length > 2) {
                        value = parts[0] + ',' + parts[1];
                    }


                    let integer = parts[0];

                    let decimal = parts[1] ?? null;


                    if (!integer) {
                        el.value = '';
                        return;
                    }


                    // format ribuan
                    integer = Number(integer).toLocaleString('id-ID');


                    let formatted = integer;

                    if (decimal !== null) {
                        formatted += ',' + decimal;
                    }


                    el.value = formatted;


                    let diff = formatted.length - value.length;

                    el.setSelectionRange(
                        cursor + diff,
                        cursor + diff
                    );


                JS,

            ])


            ->dehydrateStateUsing(function ($state) {

                if (blank($state)) {
                    return null;
                }


                // hapus titik ribuan
                $state = str_replace('.', '', $state);


                // ubah koma desimal menjadi titik
                $state = str_replace(',', '.', $state);


                return number_format(
                    (float) $state,
                    2,
                    '.',
                    ''
                );

            });
    }
}