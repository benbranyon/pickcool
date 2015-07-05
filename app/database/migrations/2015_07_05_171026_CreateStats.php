<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStats extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('candidate_stats', function(Blueprint $table)
		{
			$table->integer('id');
      foreach(Contest::$intervals as $i)
      {
        $table->integer('vote_count_'.$i)->default(0);
        $table->integer('rank_'.$i)->default(0);
        $table->index('vote_count_'.$i);
        $table->index('rank_'.$i);
      }
      $table->datetime('first_voted_at')->nullable();
      $table->unique('id');
		});
    
		Schema::create('contest_stats', function(Blueprint $table)
		{
			$table->integer('id');
      foreach(Contest::$intervals as $i)
      {
        $table->integer('vote_count_'.$i)->default(0);
        $table->index('vote_count_'.$i);
      }
      $table->unique('id');
		});
    
    
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('candidate_stats');
		Schema::drop('contest_stats');
	}

}
