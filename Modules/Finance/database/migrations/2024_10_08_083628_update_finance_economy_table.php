<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Finance\Models\FinanceEconomy;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('finance_economies', function (Blueprint $table) {
            $table->integer('project_id')->unique()->change();
            $table->string('status')->default(FinanceEconomy::STATUS_NEW)->index();
            $table->integer('job_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_economies', function (Blueprint $table) {
            $table->integer('project_id')->unique(false)->change();
            $table->dropColumn('status');
            $table->dropColumn('job_id');
        });
    }
};
