<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGame extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->boolean('is_visible')->default(true);
			$table->integer('earned_points')->default(0);
      $table->integer('pending_points')->default(0);
      $table->integer('rank')->default(0);
      $table->datetime('most_recent_voted_at');
      $table->index('earned_points');
      $table->index('pending_points');
      $table->index('rank');
      $table->index('is_visible');
      $table->index('most_recent_voted_at');
		});
    User::query()->update(['is_visible'=>false]);
    
		Schema::table('votes', function(Blueprint $table)
		{
      $table->integer('votes_ahead')->default(0);
			$table->datetime('voted_at');
      $table->index('votes_ahead');
      $table->index('voted_at');
		});
    DB::update(DB::raw("update votes set voted_at = updated_at"));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('is_visible');
			$table->dropColumn('earned_points');
			$table->dropColumn('pending_points');
      $table->dropColumn('rank');
      $table->dropColumn('most_recent_voted_at');
		});
		Schema::table('votes', function(Blueprint $table)
		{
			$table->dropColumn('voted_at');
      $table->dropColumn('votes_ahead');
		});
    
	}

}
