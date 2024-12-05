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
        Schema::table('repository_server', function (Blueprint $table) {
            $table->removeColumn('url');
            $table->string('resource_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repository_server', function (Blueprint $table) {
            $table->removeColumn('resource_url');
            $table->string('url')->nullable();
        });
    }
};
