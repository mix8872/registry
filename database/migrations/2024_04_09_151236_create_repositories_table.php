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
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('project_id')->index();
            $table->string('name')->index()->unique();
            $table->text('comment')->nullable();
            $table->string('url')->unique()->nullable();
        });

        Schema::create('repository_server', function (Blueprint $table) {
            $table->unsignedBigInteger('repository_id')->index();
            $table->unsignedBigInteger('server_id')->index();
            $table->string('type')->index();
            $table->string('url');

            $table->foreign('repository_id')
                ->references('id')
                ->on('repositories')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('server_id')
                ->references('id')
                ->on('servers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repository_server');
        Schema::dropIfExists('repositories');
    }
};
