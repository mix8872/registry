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
        Schema::table('finance_economies', function (Blueprint $table) {
            $table->decimal('profit', 15)->default(0);
            $table->decimal('performance', 15)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_economies', function (Blueprint $table) {
            $table->dropColumn('profit');
            $table->dropColumn('performance');
        });
    }
};
