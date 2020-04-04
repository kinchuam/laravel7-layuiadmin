<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccesslogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path',120)->index();
            $table->string('method', 10);
            $table->text('input');
            $table->tinyInteger('type')->default(0);
            $table->string('ip',120)->index();
            $table->string('platform',100)->comment('系统')->nullable()->index();
            $table->string('browser',100)->comment('浏览器')->nullable()->index();
            $table->text('agent')->comment('user_agent');
            $table->text('ipdata');
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
        Schema::dropIfExists('access_log');
    }
}
