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
            $table->string('locale', 5)->default('cs')->after('email_verified_at');
            $table->string('currency', 3)->default('CZK')->after('locale');
            $table->string('number_format')->default('cs')->after('currency');
            $table->string('date_format')->default('cs')->after('number_format');
            $table->string('timezone')->default('Europe/Prague')->after('date_format');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'locale',
                'currency',
                'number_format',
                'date_format',
                'timezone',
            ]);
        });
    }
};
