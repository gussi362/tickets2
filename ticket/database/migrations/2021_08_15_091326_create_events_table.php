<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->String('name_ar');
            $table->String('name_en');
            $table->longText('details_ar');
            $table->longText('details_en');
            $table->date('first_date');
            $table->date('last_date');
            $table->integer('ticket_count')->unsigned();
            $table->string('image');
            $table->string('status',5);
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->boolean('sponser')->default(0);
            $table->string('coordinates');// map directions
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::disableForeignKeyConstraints();//ignore foriegn keys when refreshing migration
        Schema::dropIfExists('events');
        Schema::enableForeignKeyConstraints();
    }
}
