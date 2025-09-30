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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('ico', 8)->nullable()->index()->after('phone');
            $table->string('dic', 15)->nullable()->after('ico');
            $table->json('company_registry_data')->nullable()->after('dic');
            $table->timestamp('registry_updated_at')->nullable()->after('company_registry_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'ico',
                'dic',
                'company_registry_data',
                'registry_updated_at',
            ]);
        });
    }
};
