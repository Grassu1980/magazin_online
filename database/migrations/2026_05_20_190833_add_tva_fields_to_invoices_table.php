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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->after('client_email');
            $table->decimal('tva_amount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('tva_rate', 5, 2)->default(19)->after('tva_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'tva_amount', 'tva_rate']);
        });
    }
};
