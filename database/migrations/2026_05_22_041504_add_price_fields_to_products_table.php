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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'purchase_price_without_vat')) {
                $table->decimal('purchase_price_without_vat', 10, 2)->nullable()->after('stock');
            }
            if (!Schema::hasColumn('products', 'price_with_vat')) {
                $table->decimal('price_with_vat', 10, 2)->nullable()->after('price_without_vat');
            }
            if (!Schema::hasColumn('products', 'promo_price')) {
                $table->decimal('promo_price', 10, 2)->nullable()->after('vat_rate');
            }
            if (!Schema::hasColumn('products', 'promo_start')) {
                $table->datetime('promo_start')->nullable()->after('promo_price');
            }
            if (!Schema::hasColumn('products', 'promo_end')) {
                $table->datetime('promo_end')->nullable()->after('promo_start');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_price_without_vat',
                'price_without_vat',
                'price_with_vat',
                'vat_rate',
                'promo_price',
                'promo_start',
                'promo_end'
            ]);
        });
    }
};
