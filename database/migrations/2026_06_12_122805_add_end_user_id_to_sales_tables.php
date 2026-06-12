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
        Schema::table('sales_targets', function (Blueprint $table) {
            $table->foreignId('end_user_id')->nullable()->constrained('end_users')->onDelete('cascade');
        });

        Schema::table('sales_realizations', function (Blueprint $table) {
            $table->foreignId('end_user_id')->nullable()->constrained('end_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_targets', function (Blueprint $table) {
            $table->dropForeign(['end_user_id']);
            $table->dropColumn('end_user_id');
        });

        Schema::table('sales_realizations', function (Blueprint $table) {
            $table->dropForeign(['end_user_id']);
            $table->dropColumn('end_user_id');
        });
    }
};
