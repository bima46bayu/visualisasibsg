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
        Schema::table('profitabilities', function (Blueprint $table) {
            // Drop old unique constraint
            $table->dropUnique('profitability_unique');
            // Add new unique constraint including sub_entity_id
            $table->unique(['year', 'month', 'entity_id', 'sub_entity_id'], 'profitability_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profitabilities', function (Blueprint $table) {
            $table->dropUnique('profitability_unique');
            $table->unique(['year', 'month', 'entity_id'], 'profitability_unique');
        });
    }
};
