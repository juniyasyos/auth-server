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
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Drop the existing primary key constraint on email
            $table->dropPrimary();

            // Rename email column to nip
            $table->renameColumn('email', 'nip');

            // Make nip the new primary key
            $table->primary('nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Drop the primary key constraint on nip
            $table->dropPrimary();

            // Rename nip column back to email
            $table->renameColumn('nip', 'email');

            // Make email the primary key again
            $table->primary('email');
        });
    }
};
