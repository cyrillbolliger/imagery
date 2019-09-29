<?php

use App\Legal;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLegalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('image_id');
            $table->enum('right_of_personality', [
                Legal::PERSONALITY_NOT_APPLICABLE,
                Legal::PERSONALITY_AGREEMENT,
                Legal::PERSONALITY_PUBLIC_INTEREST,
                Legal::PERSONALITY_UNKNOWN,
                Legal::PERSONALITY_NO_AGREEMENT,
            ]);
            $table->enum('originator_type', [
                Legal::ORIGINATOR_USER,
                Legal::ORIGINATOR_STOCK,
                Legal::ORIGINATOR_AGENCY,
                Legal::ORIGINATOR_FRIEND,
                Legal::ORIGINATOR_UNKNOWN,
            ]);
            $table->enum('licence', [
                Legal::LICENCE_CC,
                Legal::LICENCE_CC_ATTRIBUTION,
                Legal::LICENCE_OTHER,
            ])->nullable();
            $table->string('originator');
            $table->text('stock_url')->nullable(); // up to 2048 chars
            $table->boolean('shared');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('image_id')
                  ->references('id')
                  ->on('images')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('legals');
    }
}
