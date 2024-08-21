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
        Schema::table('ip_addresses', function (Blueprint $table) {
            $table->integer('server_id')->unique(false)->change();
            $table->unique([
                'server_id',
                'ip_addr',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ip_addresses', function (Blueprint $table) {
            $table->dropUnique('ip_addresses_server_id_ip_addr_unique');
            $table->integer('server_id')->unique()->change();
        });
    }
};
