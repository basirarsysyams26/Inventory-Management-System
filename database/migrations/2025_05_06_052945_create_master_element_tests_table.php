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
        Schema::create('master_element_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_equipment_type_id')->constrained('product_equipment_types')->onDelete('cascade');
            $table->string('nama_element');
            $table->string('parameter');
            $table->string('keterangan_ok');
            $table->string('keterangan_not_ok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_element_tests');
    }
};