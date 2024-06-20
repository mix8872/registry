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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->index()->unique();
            $table->text('comment')->nullable();
            $table->text('checklist')->nullable();
            $table->boolean('is_public_nat')->default(false);
            $table->string('creds_url');
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
        Schema::dropIfExists('servers');
    }
};
