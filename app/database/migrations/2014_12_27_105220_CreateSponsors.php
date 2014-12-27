<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSponsors extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sponsors', function(Blueprint $table)
		{
			$table->increments('id');
      $table->string('name');
      $table->longtext('description');
      $table->integer('image_id');
      $table->string('url');
			$table->timestamps();
		});
		Schema::create('contest_sponsor', function(Blueprint $table)
		{
			$table->increments('id');
      $table->integer('contest_id');
      $table->integer('sponsor_id');
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
		Schema::drop('sponsors');
		Schema::drop('contest_sponsor');
	}

}
