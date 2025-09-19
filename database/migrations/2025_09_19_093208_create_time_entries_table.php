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
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('task_id')->nullable()->constrained();
            $table->text('description');
            $table->integer('duration'); // in minutes
            $table->decimal('hourly_rate', 10, 2);
            $table->boolean('billable')->default(true);
            $table->boolean('billed')->default(false);
            $table->foreignId('invoice_item_id')->nullable();
            $table->date('date');
            $table->time('started_at')->nullable();
            $table->time('ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
