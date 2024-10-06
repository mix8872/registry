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
        Schema::create('finance_spent_facts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('project_id')->index();
            $table->integer('crm_id')->nullable();
            $table->dateTime('date');
            $table->integer('count');
            $table->integer('finance_res_id')->index();
            $table->string('task_url')->nullable();
            $table->string('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_spent_facts');
    }
};
