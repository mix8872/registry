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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('client')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('legal_customer')->nullable();
            $table->string('legal_inner')->nullable();
            $table->string('contract')->nullable();
            $table->date('contract_date')->nullable();
            $table->date('contract_close_date')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('payment_period')->nullable();
            $table->integer('work_type_id')->nullable();
            $table->float('cost')->default(0);

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('work_type_id')
                ->references('id')
                ->on('work_types')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('client');
            $table->dropColumn('customer_id');
            $table->dropColumn('legal_customer');
            $table->dropColumn('legal_inner');
            $table->dropColumn('contract');
            $table->dropColumn('contract_date');
            $table->dropColumn('contract_close_date');
            $table->dropColumn('payment_type');
            $table->dropColumn('payment_period');
            $table->dropColumn('work_type_id');
            $table->dropColumn('cost');
        });
    }
};
