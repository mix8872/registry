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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->index()->unique();
            $table->text('comment')->nullable();
            $table->string('status')->index();
            $table->integer('crm_id')->index()->nullable();
            $table->string('crm_url')->index()->nullable();
            $table->string('creds_url')->index()->nullable();
            $table->integer('created_by')->index();
            $table->integer('updated_by')->index();

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
