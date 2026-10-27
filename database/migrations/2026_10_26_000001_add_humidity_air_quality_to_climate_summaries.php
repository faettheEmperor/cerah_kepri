<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('climate_daily_summaries', function (Blueprint $table) {
            $table->unsignedSmallInteger('relative_humidity_mean')->nullable();
            $table->decimal('pm2_5_mean_ug_m3', 7, 2)->nullable();
            $table->decimal('us_aqi_mean', 7, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('climate_daily_summaries', function (Blueprint $table) {
            $table->dropColumn(['relative_humidity_mean', 'pm2_5_mean_ug_m3', 'us_aqi_mean']);
        });
    }
};
