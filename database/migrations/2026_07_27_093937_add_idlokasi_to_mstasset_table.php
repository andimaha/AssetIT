<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table('mstasset', function (Blueprint $table) {


            $table->unsignedBigInteger('IDLokasi')
                ->nullable()
                ->after('IDPerusahaan');


            $table->foreign('IDLokasi')
                ->references('IDLokasi')
                ->on('mstlokasi')
                ->nullOnDelete();


        });

    }



    public function down(): void
    {

        Schema::table('mstasset', function (Blueprint $table) {


            $table->dropForeign([
                'IDLokasi'
            ]);


            $table->dropColumn('IDLokasi');


        });

    }

};