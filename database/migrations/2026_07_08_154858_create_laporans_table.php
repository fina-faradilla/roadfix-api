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
            $table->id();
            
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('kategori_id')->constrained('kategori_kerusakan');
            
            $table->string('judul');
            $table->text('deskripsi');
            
            $table->string('foto')->nullable();
            
            $table->string('alamat');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->enum('tingkat_kerusakan', [
                'Ringan',
                'Sedang',
                'Berat'
            ]);
            
            $table->enum('status', [
                'Menunggu',
                'Diproses',
                'Selesai'
            ])->default('Menunggu');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
