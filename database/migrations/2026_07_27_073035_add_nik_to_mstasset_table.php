<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::table('mstasset', function (Blueprint $table) {

            $table->string('NIK', 30)
                ->nullable()
                ->after('IDPerusahaan');


            $table->foreign('NIK')
                ->references('NIK')
                ->on('mstkaryawan')
                ->cascadeOnDelete();

        });
    }



    public function down(): void
    {
        Schema::table('mstasset', function (Blueprint $table) {

            $table->dropForeign(['NIK']);

            $table->dropColumn('NIK');

        });
    }

};