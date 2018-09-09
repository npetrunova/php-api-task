<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriberFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriber_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->interger('subsciber_id');
            $table->integer('field_id');
            $table->string('value');
            $table->timestamps();
            $table->foreign('subscriber_id')->reference('id')->on('subscibers')->onDelete('cascade');
            $table->foreign('field_id')->reference('id')->on('fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriber_fields');
    }
}
