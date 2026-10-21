<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weather_observations', function (Blueprint $table) {
            $table->decimal('pm2_5_ug_m3', 7, 2)->nullable()->after('visibility_m');
        });
    }

    public function down(): void
    {
        Schema::table('weather_observations', function (Blueprint $table) {
            $table->dropColumn('pm2_5_ug_m3');
        });
    }
};
