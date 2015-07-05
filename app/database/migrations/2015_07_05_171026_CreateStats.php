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
    Schema::table('candidates', function(Blueprint $table)
    {
      foreach(Contest::$intervals as $i)
      {
        $table->integer('vote_count_'.$i)->default(0);
        $table->integer('rank_'.$i)->default(0);
      }
      $table->datetime('first_voted_at')->nullable();
    });

    Schema::table('contests', function(Blueprint $table)
    {
      foreach(Contest::$intervals as $i)
      {
        $table->integer('vote_count_'.$i)->default(0);
      }
    });
    
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('candidates', function(Blueprint $table)
		{
      foreach(Contest::$intervals as $i)
      {
        $table->dropColumn('vote_count_'.$i);
        $table->dropColumn('rank_'.$i);
      }
      $table->dropColumn('first_voted_at');
		});
		Schema::table('contests', function(Blueprint $table)
		{
      foreach(Contest::$intervals as $i)
      {
        $table->dropColumn('vote_count_'.$i);
      }
		});
	}

}
