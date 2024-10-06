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
        Schema::create('finance_economy_spents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('finance_economy_id')->index();
            $table->integer('finance_res_id')->index();
            $table->float('rate_in')->default(0);
            $table->float('rate_out')->default(0);
            $table->integer('sold_count')->nullable();
            $table->integer('spent_count')->nullable();
            $table->float('price_in')->default(0);
            $table->float('price_out')->default(0);
            $table->float('performance')->default(0);
            $table->float('profit')->default(0);

            $table->foreign('finance_economy_id')->references('id')->on('finance_economies')->onDelete('cascade');
            $table->foreign('finance_res_id')->references('id')->on('finance_res')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_economy_spents');
    }
};
