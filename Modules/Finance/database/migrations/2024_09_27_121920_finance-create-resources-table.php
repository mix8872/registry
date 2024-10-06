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
        Schema::create('finance_res', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('crm_id')->nullable();
            $table->string('name')->index();
            $table->string('type')->index()->default('internal');
            $table->integer('cost_in')->nullable();
            $table->integer('cost_out');
            $table->string('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_res');
    }
};
