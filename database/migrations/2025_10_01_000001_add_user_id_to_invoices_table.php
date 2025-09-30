<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
        });

        // Backfill user_id from related project or client, otherwise fallback to first user
        if (Schema::hasTable('invoices') && Schema::hasTable('users')) {
            $firstUserId = optional(DB::table('users')->select('id')->orderBy('id')->first())->id;

            DB::table('invoices')->orderBy('id')->chunkById(200, function ($invoices) use ($firstUserId) {
                foreach ($invoices as $inv) {
                    if (! is_null($inv->user_id)) {
                        continue;
                    }

                    $userId = null;

                    if ($inv->project_id && Schema::hasTable('projects')) {
                        $userId = DB::table('projects')->where('id', $inv->project_id)->value('user_id');
                    }

                    if (! $userId && $inv->client_id && Schema::hasTable('clients')) {
                        $userId = DB::table('clients')->where('id', $inv->client_id)->value('user_id');
                    }

                    if (! $userId) {
                        $userId = $firstUserId;
                    }

                    if ($userId) {
                        DB::table('invoices')->where('id', $inv->id)->update(['user_id' => $userId]);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};


