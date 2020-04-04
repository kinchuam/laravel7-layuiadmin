<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('category_id')->default(0)->comment('分类id')->index();
            $table->string('title',200)->comment('标题')->index();
            $table->string('author',90)->nullable()->comment('作者');
            $table->string('tags',500)->nullable()->comment('标签');
            $table->string('keywords',200)->nullable()->comment('关键词')->index();
            $table->string('description')->nullable()->comment('描述');
            $table->longText('content')->comment('内容');
            $table->integer('click')->default(0)->comment('点击量');
            $table->string('thumb',200)->nullable()->comment('缩略图');
            $table->tinyInteger('status')->default(0)->comment('状态');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('ishelp')->default(0);
            $table->tinyInteger('ishome')->default(0);
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
        Schema::dropIfExists('articles');
    }
}
