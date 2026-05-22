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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('tva_status')->nullable()->after('is_active');
            $table->date('tva_valid_from')->nullable()->after('tva_status');
            $table->date('tva_valid_to')->nullable()->after('tva_valid_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['tva_status', 'tva_valid_from', 'tva_valid_to']);
        });
    }
};
