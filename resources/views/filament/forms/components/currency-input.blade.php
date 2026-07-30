<?php

namespace App\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class CurrencyInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->prefix('Rp')
            ->mask(RawJs::make('$money($input)'))
            ->stripCharacters(',')
            ->dehydrateStateUsing(fn ($state) => preg_replace('/[^0-9]/', '', $state))
            ->numeric();
    }
}