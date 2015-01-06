<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CleanUpCandidates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('candidates', function(Blueprint $table)
		{
			$table->dropColumn('buy_url');
			$table->dropColumn('buy_text');
			$table->dropColumn('previous_rank');
			$table->dropColumn('current_rank');
			$table->dropColumn('total_votes');
      $table->integer('user_id')->after('id')->nullable();
      $table->integer('vote_boost')->after('image_id')->default(0);
		});
    DB::update('update candidates set user_id = (select id from users where users.fb_id = candidates.fb_id) where fb_id is not null');
		Schema::table('candidates', function(Blueprint $table)
		{
			$table->dropColumn('fb_id');
		});
    
		Schema::table('votes', function(Blueprint $table)
		{
  		$table->index(['user_id', 'contest_id']);
      $table->index('candidate_id');
      $table->index('created_at');
      $table->index('updated_at');
      $table->index(['candidate_id','updated_at']);
      $table->index(['candidate_id','user_id',]);
		});
    
		Schema::table('contests', function(Blueprint $table)
		{
			$table->dropColumn('vote_count');
      $table->dropColumn('vote_count_hot');
		});
    DB::insert('insert into votes (user_id, candidate_id, contest_id, created_at, updated_at) select 0,id,contest_id, utc_timestamp() - interval 5 day,utc_timestamp() - interval 5 day from candidates');
    DB::insert('insert into votes (user_id, candidate_id, contest_id, created_at, updated_at) select 0,id,contest_id, utc_timestamp() - interval 5 day,utc_timestamp() - interval 5 day from candidates');
    
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

	}

}
