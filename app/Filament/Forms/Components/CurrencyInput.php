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
            ->mask(RawJs::make(<<<'JS'
                $input => {
                    let value = $input.replace(/\D/g, '');

                    return new Intl.NumberFormat('id-ID')
                        .format(value);
                }
            JS))
            ->dehydrateStateUsing(function ($state) {
                return str_replace('.', '', $state);
            });
    }
}