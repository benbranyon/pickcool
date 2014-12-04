<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('images', function(Blueprint $table)
		{
			$table->increments('id');
      $table->string('url');
      $table->string('image_file_name')->nullable();
      $table->integer('image_file_size')->nullable();
      $table->string('image_content_type')->nullable();
      $table->timestamp('image_updated_at')->nullable();
			$table->string('md5')->nullable();
      $table->integer('created_at');
      $table->integer('updated_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('images');
	}

}
