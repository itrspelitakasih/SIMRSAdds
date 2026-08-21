<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_drive_settings', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->text('refresh_token')->nullable();
            $table->string('folder_id')->nullable();
            $table->string('connected_email')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_drive_settings');
    }
};
