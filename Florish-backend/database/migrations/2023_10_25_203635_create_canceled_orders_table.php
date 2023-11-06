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
        Schema::create('canceled_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->integer('quantity');
           $table->float('total', 10, 2);
            $table->date('canceled_date');
            $table->unsignedBigInteger('user_id');
            $table->string('reason');
            $table->string('action_taken');
            $table->timestamps();
        });

        // Define foreign key relationship outside the create method
        Schema::table('canceled_orders', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canceled_orders');
    }
};
