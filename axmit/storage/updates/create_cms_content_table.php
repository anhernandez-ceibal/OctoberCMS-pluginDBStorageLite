<?php namespace Axmit\DbDatasource\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateCmsContentTable extends Migration
{
    public function up()
    {
        Schema::create(
            'axmit_storage_cms_content', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('directory');
            $table->string('filename');
            $table->string('extension');
            $table->text('content');
            $table->mediumText('settings');
            $table->timestamps();

            $table->index('directory');
            $table->index('filename');
            $table->unique(['directory', 'filename', 'extension'], 'dir_filename_ext_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('axmit_storage_cms_content');
    }
}
