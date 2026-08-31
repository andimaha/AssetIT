<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trxretireasset', function (Blueprint $table) {

            $table->string('Kondisi', 100)
                  ->default('Others')
                  ->after('AlasanRetire');

        });
    }

    public function down(): void
    {
        Schema::table('trxretireasset', function (Blueprint $table) {

            $table->dropColumn('Kondisi');

        });
    }
};
