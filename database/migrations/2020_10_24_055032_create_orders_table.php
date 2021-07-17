<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->index()->unsigned();
            $table->foreign('client_id')->references('id')->on('users')->onUpdate('cascade');
            $table->integer('staff_id')->index()->unsigned()->nullable();
            $table->foreign('staff_id')->references('id')->on('staff')->onUpdate('cascade');
            $table->string('comments');
            $table->string('address');
            $table->string('cartItems');
            $table->string('total');
            $table->integer('quantity')->default(1);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
