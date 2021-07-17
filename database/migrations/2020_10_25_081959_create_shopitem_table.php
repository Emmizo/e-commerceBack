<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopitemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopitems', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id')->index()->unsigned();
            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade');
            $table->integer('shop_id')->index()->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onUpdate('cascade');
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
        Schema::dropIfExists('shopitem');
    }
}
