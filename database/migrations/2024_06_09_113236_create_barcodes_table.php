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
        Schema::create('barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('value')->unique();
            $table->time('time_in_valid_from')->nullable(); // waktu absensi masuk dibuka (cth: 05:00)
            $table->time('time_in_valid_until'); // batas waktu absensi masuk (cth: 09:00)
            $table->time('time_out_valid_from')->nullable(); // waktu mulai absensi keluar (cth: 15:00)
            $table->time('time_out_valid_until')->nullable(); // batas waktu absensi keluar (cth: 17:00)
            $table->geography('coordinates', 'POINT'); // lokasi barcode
            $table->float('radius'); // jarak maksimal absen dari lokasi barcode (dalam meter)
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcodes');
    }
};
