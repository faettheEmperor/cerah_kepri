<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->string('code', 20);
            $table->string('name');
            $table->string('type', 20);
            $table->string('parent_code', 20)->nullable()->index();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('active')->default(true);
            $table->timestampsTz();
            $table->primary('code');
            $table->index(['type', 'parent_code']);
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->foreign('parent_code')->references('code')->on('regions')->nullOnDelete();
        });

        Schema::create('weather_observations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('region_code', 20);
            $table->string('kind', 16);
            $table->timestampTz('valid_at');
            $table->timestampTz('fetched_at')->index();
            $table->decimal('temperature_c', 5, 2)->nullable();
            $table->unsignedSmallInteger('relative_humidity')->nullable();
            $table->decimal('wind_speed_kmh', 6, 2)->nullable();
            $table->unsignedSmallInteger('wind_direction_deg')->nullable();
            $table->unsignedSmallInteger('weather_code')->nullable();
            $table->decimal('rain_mm', 7, 2)->nullable();
            $table->unsignedTinyInteger('precipitation_probability')->nullable();
            $table->unsignedInteger('visibility_m')->nullable();
            $table->decimal('apparent_temperature_c', 5, 2)->nullable();
            $table->decimal('uv_index', 5, 2)->nullable();
            $table->decimal('soil_moisture_0_1cm', 6, 4)->nullable();
            $table->decimal('et0_mm', 7, 3)->nullable();
            $table->timestampsTz();
            $table->foreign('region_code')->references('code')->on('regions')->cascadeOnDelete();
            $table->index(['region_code', 'kind', 'valid_at']);
            $table->index(['region_code', 'fetched_at']);
        });

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

        Schema::create('weather_sync_runs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source', 20);
            $table->timestampTz('started_at');
            $table->timestampTz('finished_at')->nullable();
            $table->string('status', 20)->default('running');
            $table->unsignedInteger('records_written')->default(0);
            $table->text('error_message')->nullable();
            $table->timestampsTz();
            $table->index(['source', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_sync_runs');
        Schema::dropIfExists('marine_observations');
        Schema::dropIfExists('weather_observations');
        Schema::dropIfExists('regions');
    }
};
