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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->foreignId('no_ic'); 
            $table->string('nama_exco');
            $table->date('tarikh_laporan');
            $table->date('tarikh_hantar')->nullable();
            $table->text('sebab_hantar_semula')->nullable();
            $table->string('status_laporan')->default('draf');
            $table->timestamps();
        });

        Schema::create('butiran_laporans', function (Blueprint $table) {
            $table->id('id_butiran_laporan');
            $table->foreignId('id_laporan');
            $table->foreignId('id_dorm');
            $table->string('jenis_butiran');
            $table->text('deskripsi_isu')->nullable();
            $table->json('data_tambahan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
        Schema::dropIfExists('butiran_laporans');
    }
};
