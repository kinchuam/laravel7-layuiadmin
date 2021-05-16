<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->default('');
            $table->string('filename',150)->default('')->index();
            $table->string('path',200)->default('');
            $table->string('suffix',20)->default('');
            $table->string('type',100)->default('');
            $table->string('storage',50)->default('');
            $table->string('file_url',200)->default('');
            $table->integer('size')->default(0);
            $table->integer('group_id')->default(0)->index();
            $table->softDeletes();
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
        Schema::dropIfExists('attachment');
    }
}
