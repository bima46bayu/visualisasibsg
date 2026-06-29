<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop JSON columns from profitabilities
        Schema::table('profitabilities', function (Blueprint $table) {
            $table->dropColumn([
                'pendapatan_items',
                'hpp_items',
                'biaya_marketing_items',
                'biaya_admin_items',
                'biaya_non_ops_items',
                'pendapatan_lain_items',
                'biaya_lain_items',
                'pajak_items'
            ]);
        });

        // 2. Create profitability_items table
        Schema::create('profitability_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profitability_id')->constrained('profitabilities')->cascadeOnDelete();
            $table->string('category');
            $table->string('description')->nullable();
            $table->decimal('amount', 20, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profitability_items');
        
        Schema::table('profitabilities', function (Blueprint $table) {
            $table->json('pendapatan_items')->nullable();
            $table->json('hpp_items')->nullable();
            $table->json('biaya_marketing_items')->nullable();
            $table->json('biaya_admin_items')->nullable();
            $table->json('biaya_non_ops_items')->nullable();
            $table->json('pendapatan_lain_items')->nullable();
            $table->json('biaya_lain_items')->nullable();
            $table->json('pajak_items')->nullable();
        });
    }
};
