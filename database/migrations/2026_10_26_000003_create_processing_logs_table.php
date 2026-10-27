<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processing_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('process', 40);
            $table->string('scope', 255)->nullable();
            $table->timestampTz('started_at');
            $table->timestampTz('finished_at')->nullable();
            $table->string('status', 20)->default('running');
            $table->unsignedInteger('records_processed')->default(0);
            $table->unsignedInteger('records_succeeded')->default(0);
            $table->unsignedInteger('records_failed')->default(0);
            $table->unsignedInteger('records_written')->default(0);
            $table->text('error_message')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestampsTz();
            $table->index(['process', 'started_at']);
            $table->index(['status', 'started_at']);
        });

        if (Schema::hasTable('weather_sync_runs')) {
            DB::table('weather_sync_runs')->where('source', 'weather')->orderBy('id')->chunk(500, function ($runs): void {
                foreach ($runs as $run) {
                    DB::table('processing_logs')->insert([
                        'process' => 'weather',
                        'scope' => 'Migrasi dari log sinkronisasi lama',
                        'started_at' => $run->started_at,
                        'finished_at' => $run->finished_at,
                        'status' => $run->status,
                        'records_processed' => $run->records_written,
                        'records_succeeded' => $run->records_written,
                        'records_failed' => $run->status === 'failed' ? 1 : 0,
                        'records_written' => $run->records_written,
                        'error_message' => $run->error_message,
                        'metadata' => json_encode(['legacy_source' => $run->source]),
                        'created_at' => $run->created_at,
                        'updated_at' => $run->updated_at,
                    ]);
                }
            });

        }
    }

    public function down(): void
    {
        Schema::dropIfExists('processing_logs');
    }
};
