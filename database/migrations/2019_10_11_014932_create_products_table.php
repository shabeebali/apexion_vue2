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
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('sku');
            $table->string('hsn');
            $table->decimal('weight',8,3);
            $table->decimal('landing_price',8,2);
            $table->decimal('mrp',8,2);
            $table->decimal('gst',8,2);
            $table->decimal('general_sp_customer',8,2);
            $table->decimal('general_sp_dealer',8,2);
            $table->integer('total_stock');
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
