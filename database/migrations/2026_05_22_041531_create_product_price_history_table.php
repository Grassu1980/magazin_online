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
        Schema::create('product_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('old_price_without_vat', 10, 2)->nullable();
            $table->decimal('new_price_without_vat', 10, 2)->nullable();
            $table->decimal('old_price_with_vat', 10, 2)->nullable();
            $table->decimal('new_price_with_vat', 10, 2)->nullable();
            $table->integer('old_vat_rate')->nullable();
            $table->integer('new_vat_rate')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_history');
    }
};
