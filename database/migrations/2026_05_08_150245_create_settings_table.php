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
        if (Schema::hasTable('settings')) {
            if (!Schema::hasColumn('settings', 'key') || !Schema::hasColumn('settings', 'value')) {
                Schema::table('settings', function (Blueprint $table) {
                    if (!Schema::hasColumn('settings', 'key')) {
                        $table->string('key')->unique()->after('id');
                    }
                    if (!Schema::hasColumn('settings', 'value')) {
                        $table->text('value')->nullable()->after('key');
                    }
                });
            }

            return;
        }

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
