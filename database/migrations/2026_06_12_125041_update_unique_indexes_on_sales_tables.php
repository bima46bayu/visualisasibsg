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
            $table->dropUnique('target_unique');
            $table->unique(['year', 'month', 'sales_member_id', 'entity_id', 'end_user_id'], 'target_unique');
        });

        Schema::table('sales_realizations', function (Blueprint $table) {
            $table->dropUnique('realization_unique');
            $table->unique(['year', 'month', 'sales_member_id', 'entity_id', 'end_user_id'], 'realization_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_targets', function (Blueprint $table) {
            $table->dropUnique('target_unique');
            $table->unique(['year', 'month', 'sales_member_id', 'entity_id'], 'target_unique');
        });

        Schema::table('sales_realizations', function (Blueprint $table) {
            $table->dropUnique('realization_unique');
            $table->unique(['year', 'month', 'sales_member_id', 'entity_id'], 'realization_unique');
        });
    }
};
