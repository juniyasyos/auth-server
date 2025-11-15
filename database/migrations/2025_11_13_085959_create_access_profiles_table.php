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
        Schema::create('access_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // contoh: kepala_unit, tim_mutu
            $table->string('name');           // label: "Kepala Unit", "Tim Mutu"
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false); // dilindungi, tidak bisa dihapus sembarangan
            $table->boolean('is_active')->default(true);  // bisa di-nonaktifkan tanpa dihapus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_profiles');
    }
};
