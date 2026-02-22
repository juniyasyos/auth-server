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
        Schema::table('iam_user_application_roles', function (Blueprint $table) {
            // the table already has user_id and role_id; we add application_id
            $table->foreignId('application_id')
                ->after('role_id')
                ->constrained('applications')
                ->onDelete('cascade');

            // make the unique index include application in case the same role id
            // might appear across multiple apps (defensive). we drop the old
            // constraint before recreating.
            $table->dropUnique(['user_id', 'role_id']);
            $table->unique(['user_id', 'role_id', 'application_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iam_user_application_roles', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'role_id', 'application_id']);
            $table->unique(['user_id', 'role_id']);

            $table->dropForeign(['application_id']);
            $table->dropColumn('application_id');
        });
    }
};
