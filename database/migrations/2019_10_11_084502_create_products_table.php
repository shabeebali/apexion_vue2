<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique(true);
            $table->string('slug')->unique(true);
            $table->string('sku')->unique(true);
            $table->string('hsn')->nullable();
            $table->decimal('weight',8,3)->default(0);
            $table->decimal('mrp',8,2)->default(0);
            $table->decimal('landing_price',8,2)->default(0);
            $table->decimal('gsp_customer',8,2)->default(0);
            $table->decimal('gsp_dealer',8,2)->default(0);
            $table->decimal('gst',8,2);
            $table->boolean('publish')->default(0);
            $table->boolean('tally')->default(0);
            $table->boolean('expirable')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
}
