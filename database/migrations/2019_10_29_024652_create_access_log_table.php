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
            $table->string('path',120);
            $table->string('method', 10);
            $table->mediumText('input');
            $table->tinyInteger('type')->default(0);
            $table->string('ip',120);
            $table->string('platform',100)->comment('系统')->nullable();
            $table->string('browser',100)->comment('浏览器')->nullable();
            $table->text('agent')->comment('user_agent');
            $table->mediumText('ipData');
            $table->timestamps();
            $table->index(['path','ip','platform','browser'], 'access_log_index');
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
