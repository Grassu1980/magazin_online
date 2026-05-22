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
            $table->string('xml_path')->nullable()->after('pdf_path');
            $table->string('efactura_status')->nullable()->after('xml_path');
            $table->text('efactura_message')->nullable()->after('efactura_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['xml_path', 'efactura_status', 'efactura_message']);
        });
    }
};
