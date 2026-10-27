<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('climate_daily_summaries', function (Blueprint $table) {
            $table->unsignedSmallInteger('weather_code')->nullable();
            $table->decimal('temperature_max_c', 5, 2)->nullable();
            $table->decimal('temperature_min_c', 5, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('climate_daily_summaries', function (Blueprint $table) {
            $table->dropColumn(['weather_code', 'temperature_max_c', 'temperature_min_c']);
        });
    }
};
