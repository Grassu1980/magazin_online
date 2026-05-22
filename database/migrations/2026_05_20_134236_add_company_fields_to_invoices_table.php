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
            $table->string('invoice_type')->default('individual')->after('total');
            $table->string('company_name')->nullable()->after('invoice_type');
            $table->string('company_cui')->nullable()->after('company_name');
            $table->string('company_reg')->nullable()->after('company_cui');
            $table->text('company_address')->nullable()->after('company_reg');
            $table->string('company_iban')->nullable()->after('company_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['invoice_type', 'company_name', 'company_cui', 'company_reg', 'company_address', 'company_iban']);
        });
    }
};
