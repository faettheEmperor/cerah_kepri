<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('climate_daily_summaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('region_code', 20);
            $table->date('observed_on');
            $table->decimal('precipitation_mm', 7, 2)->nullable();
            $table->decimal('temperature_mean_c', 5, 2)->nullable();
            $table->string('source', 30)->default('open_meteo_era5_land');
            $table->timestampsTz();
            $table->foreign('region_code')->references('code')->on('regions')->cascadeOnDelete();
            $table->unique(['region_code', 'observed_on']);
            $table->index(['region_code', 'observed_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('climate_daily_summaries');
    }
};
