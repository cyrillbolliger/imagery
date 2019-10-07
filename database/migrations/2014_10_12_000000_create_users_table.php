<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('added_by');
            $table->unsignedBigInteger('managed_by');
            $table->unsignedBigInteger('default_logo')->nullable();
            $table->boolean('super_admin')->default(false);
            $table->enum('lang', [
                User::LANG_EN,
                User::LANG_DE,
                User::LANG_FR
            ]);
            $table->integer('login_count')->default(0);
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('added_by')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('no action');
            $table->foreign('managed_by')
                  ->references('id')
                  ->on('groups')
                  ->onUpdate('cascade')
                  ->onDelete('no action');
            $table->foreign('default_logo')
                  ->references('id')
                  ->on('logos')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
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
        Schema::dropIfExists('users');
    }
}
