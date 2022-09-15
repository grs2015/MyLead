<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->longText('group_description')->nullable();
            $table->unsignedInteger('priceA')->nullable();
            $table->unsignedInteger('priceB')->nullable();
            $table->unsignedInteger('priceC')->nullable();
            $table->foreignId('product_id')->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
