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
            $table->mediumText('input');
            $table->tinyInteger('type')->default(0);
            $table->ipAddress('ip')->default('')->index();
            $table->string('platform',100)->default('')->index();
            $table->string('browser',100)->default('')->index();
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
        Schema::dropIfExists('access_log');
    }
}
