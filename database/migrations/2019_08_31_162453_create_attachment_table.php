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
            $table->string('uuid',200)->default(0);
            $table->string('filename',150)->comment('附件名');
            $table->string('path',150)->comment('路径');
            $table->string('suffix',20)->comment('附件的后缀');
            $table->string('type',100);
            $table->string('storage',50)->nullable();
            $table->string('file_url',120)->nullable();
            $table->integer('size')->default(0);
            $table->integer('group_id')->default(0)->comment('分类id');
            $table->softDeletes();
            $table->timestamps();
            $table->index('filename');
            $table->index('group_id');
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
