<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('marine_observations');
    }

    public function down(): void
    {
        Schema::create('marine_observations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('region_code', 20);
            $table->string('kind', 16);
            $table->timestampTz('valid_at');
            $table->timestampTz('fetched_at')->index();
            $table->decimal('wave_height_m', 6, 2)->nullable();
            $table->unsignedSmallInteger('wave_direction_deg')->nullable();
            $table->decimal('wave_period_s', 6, 2)->nullable();
            $table->decimal('wind_wave_height_m', 6, 2)->nullable();
            $table->decimal('swell_wave_height_m', 6, 2)->nullable();
            $table->decimal('sea_surface_temperature_c', 5, 2)->nullable();
            $table->decimal('ocean_current_kmh', 6, 2)->nullable();
            $table->unsignedSmallInteger('ocean_current_direction_deg')->nullable();
            $table->timestampsTz();
            $table->foreign('region_code')->references('code')->on('regions')->cascadeOnDelete();
            $table->index(['region_code', 'kind', 'valid_at']);
        });
    }
};
