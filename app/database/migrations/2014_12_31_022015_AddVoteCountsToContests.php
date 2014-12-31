<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVoteCountsToContests extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contests', function(Blueprint $table)
		{
			$table->integer('vote_count')->nullable();
      $table->integer('vote_count_hot')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contests', function(Blueprint $table)
		{ 
      $table->dropColumn('vote_count');
      $table->dropColumn('vote_count_hot');
		});
	}

}
