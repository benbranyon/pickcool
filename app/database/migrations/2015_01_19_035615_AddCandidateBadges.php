<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCandidateBadges extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('badges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contest_id');
     		$table->integer('candidate_id');
     		$table->string('name');
     		$table->integer('vote_weight')->nullable();
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
		Schema::drop('badges');
	}

}
