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
        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->decimal('purchase_price_without_vat', 10, 2);
            $table->integer('vat_rate')->default(21);
            $table->decimal('vat_value', 10, 2);
            $table->decimal('purchase_price_with_vat', 10, 2);
            $table->decimal('line_total_without_vat', 10, 2);
            $table->decimal('line_total_vat', 10, 2);
            $table->decimal('line_total_with_vat', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_items');
    }
};
