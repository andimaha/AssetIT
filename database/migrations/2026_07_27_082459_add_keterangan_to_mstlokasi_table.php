<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('mstlokasi', function (Blueprint $table) {

            $table->text('Keterangan')
                ->nullable()
                ->after('NamaLokasi');

        });
    }


    public function down(): void
    {
        Schema::table('mstlokasi', function (Blueprint $table) {

            $table->dropColumn('Keterangan');

        });
    }

};