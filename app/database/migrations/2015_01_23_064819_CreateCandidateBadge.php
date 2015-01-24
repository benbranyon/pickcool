<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidateBadge extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('candidate_badge', function(Blueprint $table)
		{
			$table->increments('id');
      		$table->integer('contest_id');
      		$table->integer('candidate_id');
      		$table->integer('badge_id');
			$table->timestamps();
		});

		Schema::table('badges', function($table)
		{
		    $table->dropColumn('contest_id');
		    $table->dropColumn('candidate_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('candidate_badge');
	}

}
