<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxonomyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxonomy', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique(true);
            $table->string('slug')->unique(true);
            $table->string('code_type');
            $table->unsignedInteger('code_length');
            $table->boolean('autogen');
            $table->boolean('in_pc');
            $table->string('next_code')->nullable();
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
        Schema::dropIfExists('taxonomy');
    }
}
