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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_company')->default(false)->after('email_verified_at');
            $table->string('company_name')->nullable()->after('is_company');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_company', 'company_name', 'company_cui', 'company_reg', 'company_address', 'company_iban']);
        });
    }
};
