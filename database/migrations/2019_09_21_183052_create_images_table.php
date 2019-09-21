<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('original_id')->nullable();
            $table->unsignedBigInteger('logo_id')->nullable();
            $table->string('filename');
            $table->string('hash');
            $table->integer('width');
            $table->integer('height');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
            $table->foreign('original_id')
                  ->references('id')
                  ->on('images');
//           todo: $table->foreign('logo_id')
//                  ->references('id')
//                  ->on('logos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
