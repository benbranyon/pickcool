<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAll extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('candidates', function(Blueprint $table)
		{
			$table->increments('id');
      $table->integer('contest_id');
      $table->string('name');
      $table->integer('image_id');
      $table->string('buy_url');
      $table->timestamps();
		});
    
		Schema::create('votes', function(Blueprint $table)
		{
			$table->increments('id');
      $table->integer('user_id');
			$table->integer('contest_id');
      $table->integer('candidate_id');
      $table->timestamps();
		});
    
		Schema::create('images', function(Blueprint $table)
		{
			$table->increments('id');
      $table->string('url');
			$table->string('sizes_md5')->nullable();
      $table->string('image_file_name')->nullable();
      $table->integer('image_file_size')->nullable();
      $table->string('image_content_type')->nullable();
      $table->datetime('image_updated_at')->nullable();
      $table->timestamps();
		});
    
		Schema::create('contests', function(Blueprint $table)
		{
			$table->increments('id');
      $table->integer('user_id');
      $table->string('title');
      $table->timestamps();
    });
    
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
      $table->string('fb_id');
      $table->string('first_name');
      $table->string('last_name');
      $table->string('gender');
      $table->string('email');
      $table->integer('is_contributor')->nullable();
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
		Schema::drop('candidates');
		Schema::drop('votes');
		Schema::drop('images');
		Schema::drop('contests');
		Schema::drop('users');
	}
}
