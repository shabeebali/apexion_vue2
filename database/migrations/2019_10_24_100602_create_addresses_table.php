<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tag_name');
            $table->string('line_1');
            $table->string('line_2')->nullable();
            $table->bigInteger('city_id');
            $table->bigInteger('state_id');
            $table->bigInteger('country_id');
            $table->string('pin');
            $table->bigInteger('customer_id');
            $table->decimal('init_bal',8,2);
            $table->date('init_bal_date');
            $table->boolean('publish');
            $table->boolean('tally');
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
        Schema::dropIfExists('addresses');
    }
}
