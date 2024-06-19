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
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->index()->unique();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('server_id')->index();
            $table->unsignedBigInteger('repository_id')->index();
            $table->string('compose_path');
            $table->string('config_repository_url')->nullable();
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

            $table->foreign('server_id')
                ->references('id')
                ->on('servers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('repository_id')
                ->references('id')
                ->on('repositories')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
