<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rule_entity')->nullable();
            $table->string('rule_attribute')->nullable();
            $table->string('rule_comparator')->nullable();
            $table->string('rule_value')->nullable();
            $table->bigInteger('tax_id');
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
        Schema::dropIfExists('tax_rules');
    }
}
