<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mstisp', function (Blueprint $table) {
            $table->unsignedBigInteger('IDPerusahaan')
                ->nullable()
                ->after('IDVendor');

            $table->foreign('IDPerusahaan')
                ->references('IDPerusahaan')
                ->on('mstperusahaan')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mstisp', function (Blueprint $table) {
            $table->dropForeign(['IDPerusahaan']);
            $table->dropColumn('IDPerusahaan');
        });
    }
};
