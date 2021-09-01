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
            
            $table->uuid('id');
            $table->bigInteger('ticket_id')->unsigned();
            $table->foreign('ticket_id')->references('id')->on('tickets');
            $table->bigInteger('date_id')->unsigned();
            $table->foreign('date_id')->references('id')->on('dates');
            $table->string('name');
            $table->integer('phone')->length(12)->unsigned();
            $table->integer('count')->unsigned();
            $table->integer('code',10)->unsigned();
            $table->integer('amount')->unsigned();
            $table->bigInteger('payment')->unsigned()->nullable();
            $table->foreign('payment')->references('id')->on('ttypes');//wethere payment is successful or not 
            $table->bigInteger('status')->unsigned()->default(1);
            $table->foreign('status')->references('id')->on('ttypes');//wethere payment is successful or not 
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
