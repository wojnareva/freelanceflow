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
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->json('events'); // ['invoice.created', 'payment.received', etc.]
            $table->string('secret')->nullable();
            $table->boolean('active')->default(true);
            $table->json('headers')->nullable(); // Custom headers
            $table->integer('timeout')->default(30); // Timeout in seconds
            $table->integer('retry_attempts')->default(3);
            $table->timestamp('last_triggered_at')->nullable();
            $table->string('last_status')->nullable(); // success, failed, timeout
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
