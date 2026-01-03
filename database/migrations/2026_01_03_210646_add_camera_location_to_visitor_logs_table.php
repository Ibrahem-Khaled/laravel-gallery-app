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
        Schema::table('visitor_logs', function (Blueprint $table) {
            $table->string('camera_image_path')->nullable()->after('city');
            $table->text('camera_image_base64')->nullable()->after('camera_image_path');
            $table->decimal('latitude', 10, 8)->nullable()->after('camera_image_base64');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->text('address')->nullable()->after('longitude');
            $table->decimal('location_accuracy', 8, 2)->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitor_logs', function (Blueprint $table) {
            $table->dropColumn([
                'camera_image_path',
                'camera_image_base64',
                'latitude',
                'longitude',
                'address',
                'location_accuracy'
            ]);
        });
    }
};
