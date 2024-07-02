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
            $table->double('latitude')->default(0); // lokasi barcode sumbu Y
            $table->double('longitude')->default(0); // lokasi barcode sumbu X
            $table->float('radius'); // jarak maksimal absen dari lokasi barcode (dalam meter)
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
