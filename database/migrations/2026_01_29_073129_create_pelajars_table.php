<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelajars', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('no_ic')->unique();
            $table->enum('jantina', ['L', 'P']);
            $table->foreignId('dorm_id')
                  ->constrained('dorms', 'id_dorm')
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelajars');
    }
};

