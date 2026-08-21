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
        Schema::table('whatsapp_settings', function (Blueprint $table) {
            $table->text('msg_ticket_created_admin')->nullable();
            $table->text('msg_ticket_created_reporter')->nullable();
            $table->text('msg_ticket_completed')->nullable();
            $table->text('msg_document_reminder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_settings', function (Blueprint $table) {
            $table->dropColumn([
                'msg_ticket_created_admin',
                'msg_ticket_created_reporter',
                'msg_ticket_completed',
                'msg_document_reminder',
            ]);
        });
    }
};
