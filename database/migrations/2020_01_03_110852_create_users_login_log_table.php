<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersLoginLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_login_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username',120)->default('')->index();
            $table->ipAddress('ip')->default('')->index();
            $table->string('message')->default('');
            $table->string('platform',100)->default('');
            $table->string('browser',100)->default('');
            $table->string('ip_data', 800)->default('');
            $table->mediumText('agent');
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
        Schema::dropIfExists('users_login_log');
    }
}
