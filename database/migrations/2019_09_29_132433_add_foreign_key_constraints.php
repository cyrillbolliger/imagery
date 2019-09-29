<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->foreign('added_by')
                  ->references('id')
                  ->on('users');
        });

        Schema::table('images', function ($table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
            $table->foreign('original_id')
                  ->references('id')
                  ->on('images');
            $table->foreign('logo_id')
                  ->references('id')
                  ->on('logos');
        });

        Schema::table('legals', function ($table) {
            $table->foreign('image_id')
                  ->references('id')
                  ->on('images')
                  ->onDelete('cascade');
        });

        Schema::table('logos', function ($table) {
            $table->foreign('added_by')
                  ->references('id')
                  ->on('users');
        });

        Schema::table('groups', function ($table) {
            $table->foreign('added_by')
                  ->references('id')
                  ->on('users');
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('groups')
                  ->onDelete('cascade');
        });

        Schema::table('roles', function ($table) {
            $table->foreign('group_id')
                  ->references('id')
                  ->on('groups')
                  ->onDelete('cascade');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->foreign('added_by')
                  ->references('id')
                  ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
