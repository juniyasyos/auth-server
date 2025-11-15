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
        Schema::create('iam_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->constrained('applications')
                ->onDelete('cascade');
            $table->foreignId('access_profile_role_iam_map')
                ->nullable()
                ->constrained('access_profiles')
                ->onDelete('set null');
            $table->string('slug'); // admin, viewer, officer, etc.
            $table->string('name'); // "Admin SIIMUT", "Viewer Incident Report"
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false); // Protected system roles
            $table->timestamps();

            // Ensure slug is unique per application
            $table->unique(['application_id', 'slug']);
            $table->index(['application_id', 'is_system']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iam_roles');
    }
};
