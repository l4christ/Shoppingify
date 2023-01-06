<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_list_pivots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id');
            $table->foreignId('list_id');
            $table->foreignId('user_id');
            $table->foreignId('item_categories_id');
            $table->integer('qty')->nullable();
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
        Schema::dropIfExists('item_list_pivots');
    }
};
