<?php

use App\Image;
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
        Schema::disableForeignKeyConstraints();

        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('logo_id')->nullable();
            $table->unsignedBigInteger('original_id')->nullable();
            $table->enum('type', [
                Image::TYPE_RAW,
                Image::TYPE_FINAL
            ]);
            $table->enum('background', [
                Image::BG_GRADIENT,
                Image::BG_TRANSPARENT,
                Image::BG_CUSTOM
            ]);
            $table->text('keywords');
            $table->string('filename');
            $table->integer('width');
            $table->integer('height');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('no action');
            $table->foreign('original_id')
                  ->references('id')
                  ->on('images')
                  ->onUpdate('cascade')
                  ->onDelete('no action');
            $table->foreign('logo_id')
                  ->references('id')
                  ->on('logos')
                  ->onUpdate('cascade')
                  ->onDelete('no action');
        });

        DB::statement('ALTER TABLE images ADD FULLTEXT fulltext_index (keywords)');

        Schema::enableForeignKeyConstraints();
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
