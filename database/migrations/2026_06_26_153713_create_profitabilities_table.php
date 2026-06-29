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
        Schema::create('profitabilities', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->foreignId('entity_id')->constrained()->cascadeOnDelete();
            
            $table->json('pendapatan_items')->nullable();
            $table->json('hpp_items')->nullable();
            $table->json('biaya_marketing_items')->nullable();
            $table->json('biaya_admin_items')->nullable();
            $table->json('biaya_non_ops_items')->nullable();
            $table->json('pendapatan_lain_items')->nullable();
            $table->json('biaya_lain_items')->nullable();
            $table->json('pajak_items')->nullable();
            
            $table->decimal('pendapatan', 20, 2)->default(0);
            $table->decimal('laba_kotor', 20, 2)->default(0);
            $table->decimal('total_biaya_overhead', 20, 2)->default(0);
            $table->decimal('laba_operasi', 20, 2)->default(0);
            $table->decimal('laba_sebelum_pajak', 20, 2)->default(0);
            $table->decimal('laba_bersih', 20, 2)->default(0);

            $table->timestamps();
            
            $table->unique(['year', 'month', 'entity_id'], 'profitability_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profitabilities');
    }
};
