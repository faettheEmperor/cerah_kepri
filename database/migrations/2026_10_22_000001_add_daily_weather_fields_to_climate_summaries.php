
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('climate_daily_summaries', 'weather_code')) {
            Schema::table('climate_daily_summaries', function (Blueprint $table) {
                $table->unsignedSmallInteger('weather_code')->nullable();
            });
        }

        if (!Schema::hasColumn('climate_daily_summaries', 'temperature_max_c')) {
            Schema::table('climate_daily_summaries', function (Blueprint $table) {
                $table->decimal('temperature_max_c', 5, 2)->nullable();
            });
        }

        if (!Schema::hasColumn('climate_daily_summaries', 'temperature_min_c')) {
            Schema::table('climate_daily_summaries', function (Blueprint $table) {
                $table->decimal('temperature_min_c', 5, 2)->nullable();
            });
        }
    }

    public function down(): void
    {
        // Jangan menghapus kolom yang mungkin sudah ada
        // sebelum migrasi ini dijalankan.
    }
};
