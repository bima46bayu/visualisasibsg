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
            $table->foreignId('sub_entity_id')->nullable()->after('entity_id')->constrained('profitability_sub_entities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profitabilities', function (Blueprint $table) {
            $table->dropForeign(['sub_entity_id']);
            $table->dropColumn('sub_entity_id');
        });
    }
};
